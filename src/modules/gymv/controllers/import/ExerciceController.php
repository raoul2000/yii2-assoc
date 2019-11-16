<?php

namespace app\modules\gymv\controllers\import;

use Yii;
use yii\web\Controller;
use League\Csv\Exception;
use League\Csv\Reader;
use app\models\Contact;
use app\models\BankAccount;
use app\models\Transaction;
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
            foreach ($csvRecords as $offset => $record) {
                $nRecord = $this->normalizeRecord($record);
                $transaction = new Transaction([
                    'description' => $nRecord['Designation'],
                    'reference_date' => $nRecord['date']
                ]);
 
                // debit/crédit and source/destination account ----------------------

                if(is_numeric($nRecord['RECETTES']) && $nRecord['RECETTES'] > 0) {
                    $transaction->to_account_id = SessionContact::getBankAccountId();
                    $transaction->from_account_id = $this->findAccountId($nRecord);
                    $transaction->value = $nRecord['RECETTES'];
                } elseif( is_numeric($nRecord['DEPENSES']) && $nRecord['DEPENSES'] > 0 ) {
                    $transaction->to_account_id =  $this->findAccountId($nRecord);
                    $transaction->from_account_id = SessionContact::getBankAccountId();
                    $transaction->value = $nRecord['DEPENSES'];
                } else {
                    // error
                }
                
                // compute attributes -----------------------------------------------

                if( is_numeric($nRecord['Num'])) {
                    $transaction->code = $nRecord['Num'];
                    $transaction->type = 'CHQ';
                } else {
                    $transaction->type = $nRecord['Num'];
                }

                // category ---------------------------------------------------------

                $transaction = $this->assignCategory($transaction);

                if($transaction->validate()) {
                    $transaction->save();
                }
                $records['L' . $offset] = [
                    'data' => [
                        'record' => $nRecord, 
                        'transaction' => $transaction->getAttributes(),
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

    private function assignCategory($model)
    {
        return $model;
    }
    private function findAccountId($record)
    {
        return 11;
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
