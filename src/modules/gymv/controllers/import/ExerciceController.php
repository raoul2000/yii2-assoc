<?php

namespace app\modules\gymv\controllers\import;

use Yii;
use yii\web\Controller;
use League\Csv\Exception;
use League\Csv\Reader;
use app\models\Contact;
use app\models\BankAccount;
use app\models\Transaction;
use app\models\Category;
use app\modules\gymv\models\UploadForm;
use yii\web\UploadedFile;
use \app\components\helpers\DateHelper;
use \app\components\SessionContact;

/**
 * Default controller for the `gymv` module
 */
class ExerciceController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' =>  \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }
        
    public function actionIndex()
    {
        $model = new UploadForm();

        if (Yii::$app->request->isPost) {

            $model->dataFile = UploadedFile::getInstance($model, 'dataFile');

            // build temporary filepath to store uploaded file
            $uuid = \thamtech\uuid\helpers\UuidHelper::uuid();
            $uploadFilepath = Yii::getAlias('@imports/' . $uuid . '.json');

            if ($model->upload($uploadFilepath)) {
                Yii::$app->session['import'] = $uploadFilepath;
                return $this->redirect(['import-csv']);
            }
        }

        return $this->render('index', ['model' => $model]);
    }


    public function actionImportCsv()
    {
        if (!Yii::$app->session->has('import')) {
            Yii::$app->session->setFlash('warning', 'No file uploaded');
            return $this->redirect(['index']);
        }
        $importFile = Yii::$app->session['import'];
        
        $errorMessage = null;
        $records = [];
        try {
            $csv = Reader::createFromStream(fopen($importFile, 'r'));
            $csv->setDelimiter(',');
            $csv->setEnclosure('"');
            $csv->setHeaderOffset(0);
            $csvRecords = $csv->getRecords(['date', 'Num', 'Designation','RECETTES', 'DEPENSES']);

            $action = null;

            $defaultContact = Contact::findOne(57);
            $defaultAccount = BankAccount::findOne(56);
            
            $cpContact = $cpAccount = $cpCategory = null;
            foreach ($csvRecords as $offset => $record) {
                $message = [];
                $nRecord = $this->normalizeRecord($record);

                // early Code and Type (needed for existing transaction search)
                list($code, $type) = createTransactionCodeAndType($nRecord['Num']);

                $transaction = new Transaction([
                    'description'    => $nRecord['Designation'],
                    'reference_date' => $nRecord['date'],
                    'code' => $code,
                    'type' => $type
                ]);

                // search for existing transaction
                $transactionExists = Transaction::find()
                    ->where([
                        'description' => $transaction->description,
                        'reference_date' => DateHelper::toDateDbFormat($transaction->reference_date),
                        'code' => $transaction->code,
                        'type' => $transaction->type
                    ])
                    ->exists();

                if( $transactionExists ) {
                    $message[] = 'transaction found in DB : skip';
                } else {
                    $message[] = 'building transaction';

                    // find entities contact/account counter parts --------------------

                    list($cpContact, $cpAccount, $cpCategory) = $this->getCounterpartAccount($nRecord);

                    $cpContact = $cpContact != null ? $cpContact : $defaultContact;
                    $cpAccount = $cpAccount != null ? $cpAccount : $defaultAccount;

                    // debit/crédit and source/destination account ----------------------
    
                    if(is_numeric($nRecord['RECETTES']) && $nRecord['RECETTES'] > 0) {
                        $transaction->to_account_id = SessionContact::getBankAccountId();
                        $transaction->from_account_id = null;
                        $transaction->value = $nRecord['RECETTES'];
                    } elseif( is_numeric($nRecord['DEPENSES']) && $nRecord['DEPENSES'] > 0 ) {
                        $transaction->to_account_id =  null;
                        $transaction->from_account_id = SessionContact::getBankAccountId();
                        $transaction->value = $nRecord['DEPENSES'];
                    } else {
                        // error
                        $message[] = 'invalid line';
                    }
    
                    // save -------------------------------------------------------------

                    if ($cpCategory != null) {
                        if ($cpCategory->id == null) {
                            $cpCategory->setScenario(Category::SCENARIO_INSERT);
                            $cpCategory->save();
                        }
                        $transaction->category_id = $cpCategory->id;
                    }

                    if($cpContact->id == null) {
                        $cpContact->save();
                    }
                    if($cpAccount->id == null) {
                        $cpAccount->contact_id = $cpContact->id;
                        $cpAccount->save(false);
                    }
                    if($transaction->to_account_id == null) {
                        $transaction->to_account_id = $cpAccount->id;
                    }else if ($transaction->from_account_id == null) {
                        $transaction->from_account_id = $cpAccount->id;
                    }

                    if($transaction->validate()) {
                        $transaction->save();
                        $message[] = 'transaction saved';
                    } else {
                        $message[] = 'invalid transaction';
                    }
                }
 
                $records['L' . $offset] = [
                    'data' => [
                        'message' => $message,
                        'record' => $nRecord, 
                        'transaction' => $transaction->getAttributes(),
                        'counterPart' => [
                            'contact' => $cpContact ? [$cpContact->getAttributes(), $cpContact->getErrors()] : null,
                            'account' => $cpAccount ? [$cpAccount->getAttributes(), $cpAccount->getErrors()] : null,
                            'category' => $cpCategory ? [$cpCategory->getAttributes(), $cpCategory->getErrors()] : null
                        ],
                        'validation' => $transaction->getErrors()
                    ],
                    'action' => $action
                ];
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
        }

        return $this->render('result', [
            'errorMessage' => $errorMessage,
            'records' => $records
        ]);
    }

    private function createTransactionCodeAndType($value)
    {
        $code = $type = null;
        if( is_numeric($value)) {
            $code = $value;
            $type = 'CHQ';
        } else {
            $type = ($value == 'CH' ? 'CHQ' : $value);
        }    
        return [ $code, $type];    
    }
    private function getCounterpartAccount($record)
    {
        $reSalaire = '/(.*) - salaire (\d\d\/\d\d\d\d)/';

        if( preg_match($reSalaire, $record['Designation'], $matches, PREG_OFFSET_CAPTURE, 0) && $record['DEPENSES'] > 0) {
            return $this->buildSalaire($record, $matches[1][0]);
        } else {
            return [null, null, null];
        }       
    }

    private function buildSalaire($record, $name)
    {
        $name = \strtolower($name);
        $bankAccount = null;
        $contact = Contact::findOne(['name' => $name]);
        if( $contact == null) {
            $contact = new Contact([
                'name' => $name,
                'is_natural_person' => true
            ]);
        } else {
            $bankAccount = BankAccount::findOne([
                'contact_id' => $contact->id,
                'name' => ''
            ]);
        }
        if($bankAccount == null) {
            $bankAccount = new BankAccount();
            $bankAccount->name = '';
            $bankAccount->contact_id = $contact->id;    
        }

        // category 
        $category = $this->getCategoryModel('salaire');

        return [$contact, $bankAccount, $category];
    }

    private function getCategoryModel($name)
    {
        $category = Category::findOne(['name' => $name]);
        if($category == null) {
            $category = new Category([
                'name' => $name,
                'type' => \app\components\ModelRegistry::TRANSACTION
            ]);
        }
        return $category;

    }
    private function normalizeRecord($record)
    {
        $record['RECETTES'] = \floatval(str_replace(',', '.' ,$record['RECETTES']));
        $record['DEPENSES'] = \floatval(str_replace(',', '.' ,$record['DEPENSES']));
        $date = date_create_from_format('d/m/y', $record['date']);
        $record['date'] = date_format($date, 'd/m/Y');
        return $record;
    }
}
