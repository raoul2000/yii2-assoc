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
            // load CSV file ----------------------------------------
            
            $csv = Reader::createFromStream(fopen($importFile, 'r'));
            $csv->setDelimiter(',');
            $csv->setEnclosure('"');
            $csv->setHeaderOffset(0);
            $csvRecords = $csv->getRecords(['date', 'Num', 'Designation','RECETTES', 'DEPENSES']);

            // get default contact and account
            $defaultContact = $this->findOrCreateContact([
                'name' => 'autre',
                'is_natural_person' => false
            ]);
            $defaultAccount = $defaultContact->bankAccounts[0];
            
            $cpContact = $cpAccount = $cpCategory = null;
            foreach ($csvRecords as $offset => $record) {   // process each line from imported file
                $message = [];
                $nRecord = $this->normalizeRecord($record);

                // early Code and Type (needed for existing transaction search)
                list($code, $type) = $this->createTransactionCodeAndType($nRecord['Num']);

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

                    // find entities contact/category counter parts --------------------

                    list($cpContact, $cpCategory) = $this->getContactAndCategory($nRecord);

                    $cpContact = $cpContact === null ? $defaultContact : $cpContact;
                    $cpAccount = $cpContact->bankAccounts[0];

                    // debit/crédit and source/destination account ----------------------
    
                    if(is_numeric($nRecord['RECETTES']) && $nRecord['RECETTES'] > 0) {
                        $transaction->to_account_id = SessionContact::getBankAccountId();
                        $transaction->from_account_id = $cpAccount->id;
                        $transaction->value = $nRecord['RECETTES'];
                    } elseif( is_numeric($nRecord['DEPENSES']) && $nRecord['DEPENSES'] > 0 ) {
                        $transaction->to_account_id =  $cpAccount->id;
                        $transaction->from_account_id = SessionContact::getBankAccountId();
                        $transaction->value = $nRecord['DEPENSES'];
                    } else {
                        // error
                        $message[] = 'invalid line';
                    }
    
                    // save category and transaction --------------------------------------------------

                    if ($cpCategory != null) {
                        $transaction->category_id = $cpCategory->id;
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
                    ]
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

    /**
     * Compute and return the CODE and TYPE properties from the NUM column
     * 
     * @param string $value the value of the NUM column
     * @return string the actual CODE value 
     */
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

    /**
     * Based on the imported record, compute and returns the Contact and Category models.
     * 
     * If a model can't be defined, NULL is returned. The actual model creation is delegated 
     * to private methods depending on the DESCRIPTION string value and additional parameter if
     * needed.
     *
     * @param [type] $record [$contact, $category]
     * @return void
     */
    private function getContactAndCategory($record)
    {
        $reSalaire = '/(.*) *- *salaire (\d\d\/\d\d\d\d)/';
        $reCODEP = '/^CODEP *- *[Rr](e|è)glement.*$/';
        $reRemiseAdhesion = '/^Remise .* adhésions.*$/';
        
        $reRembours = '/(.*) *- *rembours.*$/';

        if( preg_match($reSalaire, $record['Designation'], $matches, PREG_OFFSET_CAPTURE, 0) && $record['DEPENSES'] > 0) {
            return $this->buildSalaire($record, $matches[1][0]);
        } else if( preg_match($reCODEP, $record['Designation']) && $record['DEPENSES'] > 0) {
            return $this->buildCODEP($record);
        } else if( preg_match($reRemiseAdhesion, $record['Designation']) && $record['RECETTES'] > 0) {
            return $this->buildRemiseAdhesion($record);
        } else if( preg_match($reRembours, $record['Designation'], $matches, PREG_OFFSET_CAPTURE, 0) && $record['DEPENSES'] > 0) {
            return $this->buildRemboursement($record, $matches[1][0]);
        } else {
            return [null, null];
        }       
    }
    
    /**
     * For a remboursement returns only the contact model and NULL for 
     * the category model
     *
     * @param [type] $record
     * @param [type] $name name of the person receveing the refund
     * @return void
     */
    private function buildRemboursement($record, $name)
    {
        $contact = $this->findOrCreateContact([
            'name' => \strtolower($name),
            'is_natural_person' => true
        ]);

        return [$contact, null];
    }

    /**
     * Build and return Contact and Categpry models for record "Salaire"
     *
     * @param [type] $record current imported record
     * @param [type] $name Name for the Salary beneficiary
     * @param [type] $record [$contact, $category]
     */
    private function buildSalaire($record, $name)
    {
        $contact = $this->findOrCreateContact([
            'name' => \strtolower($name),
            'is_natural_person' => true
        ]);

        // category 
        $category = $this->finrOrCreateCategory('salaire');

        return [$contact, $category];
    }

    /**
     * Build and return Contact and Category models for
     * transaction to CODEP
     *
     * @param [type] $record
     * @return void
     */
    private function buildCODEP($record)
    {
        $contact = $this->findOrCreateContact([
            'name' => 'CODEP',
            'is_natural_person' => false
        ]);

        // category 
        $category = $this->finrOrCreateCategory('License');

        return [$contact, $category];
    }
    /**
     * Create Contact and Category for record type Adhésion
     *
     * @param [type] $record
     * @return void
     */
    private function buildRemiseAdhesion($record)
    {
        // category 
        $category = $this->finrOrCreateCategory('Adhésion');

        return [null, $category];
    }

    private function findOrCreateContact($attributes)
    {
        $contact = Contact::findOne($attributes);
        if( $contact == null) {
            $contact = new Contact($attributes);
            $contact->save();
            $bankAccount = new BankAccount([
                'contact_id' => $contact->id,
                'name' => ''
            ]);
            $bankAccount->save();
        }         
        return $contact;
    }

    private function finrOrCreateCategory($name)
    {
        $attributes = [
            'name' => $name,
            'type' => \app\components\ModelRegistry::TRANSACTION
        ];
        $category = Category::findOne($attributes);
        if($category == null) {
            $category = new Category($attributes);
            $category->save();
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
