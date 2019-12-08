<?php

namespace app\modules\gymv\controllers\import;

use Yii;
use yii\web\Controller;
use League\Csv\Exception;
use League\Csv\Reader;
use app\models\Contact;
use app\models\BankAccount;
use app\models\Address;
use app\modules\gymv\models\UploadForm;
use yii\web\UploadedFile;
use \app\components\helpers\DateHelper;

/**
 * Default controller for the `gymv` module
 */
class IReseauController extends Controller
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
            //$csv = Reader::createFromPath('d:\\tmp\\licencies.csv', 'r');
            //$csv = Reader::createFromStream(fopen('d:\\tmp\\licencies-small.csv', 'r'));
            $csv = Reader::createFromStream(fopen($importFile, 'r'));
            //$csv = Reader::createFromStream(fopen('d:\\tmp\\licencies.csv', 'r'));
            $csv->setDelimiter(',');
            $csv->setEnclosure('\'');
            $csv->setHeaderOffset(0);
            $csvRecords = $csv->getRecords(['name', 'woman_name', 'firstname','gender', 'birthday', 'license_num',
            'license_cat','residence','locality','street','zip','city','country','phone', 'mobile', 'email']);


            foreach ($csvRecords as $offset => $record) {
                $message = [];
                $contactAvailable = false;
                $normalizedRecord = $this->normalizeRecord($record);

                $contactAttributes = [
                    'name'      => $normalizedRecord['name'],
                    'firstname' => $normalizedRecord['firstname'],
                    'gender'    => $normalizedRecord['gender'],
                    'birthday'  => $normalizedRecord['birthday'],
                    'is_natural_person' => true
                ];

                $contact = Contact::find()
                    ->where($contactAttributes)
                    ->one();

                if ( $contact !== null) {
                    // contact found : skip this contact
                    $message[] = 'contact exist (skip) : ' 
                        . $normalizedRecord['name']
                        . ' '
                        .  $normalizedRecord['firstname'];
                    $contact = null;
                } else {
                    // contact does not exist in DB : insert it and create
                    // its default bank account
                    $contact = new Contact($contactAttributes);
                    $contact->setAttributes([
                        'email' => $normalizedRecord['email'],
                    ]);
                    
                    if ($contact->save()) {
                        $bankAccount = new BankAccount();
                        $bankAccount->contact_id = $contact->id;
                        $bankAccount->name = '';
                        $bankAccount->save(false);
                        $contactAvailable = true;
                    } 
                }

                if ($contactAvailable) {
                    // We have a contact record saved in DB, now let's work on the related address
                    // for this contact
                    $address = new Address([
                        'line_1'   => $normalizedRecord['street'],
                        'line_2'   => $normalizedRecord['residence'],
                        'zip_code' => $normalizedRecord['zip'],
                        'city'     => $normalizedRecord['city'],
                        'country'  => $normalizedRecord['country']
                    ]);

                    $insertAddress = false;
                    if ($contact->hasAddress) {

                        // the contact is already linked to an address : let's check
                        // if that's the same address as the one defined in the imported record

                        $existingAddress = $contact->address;
                        if( 
                            $existingAddress->line_1   !=  $address->line_1   ||
                            $existingAddress->line_2   !=  $address->line_2   ||
                            $existingAddress->zip_code !=  $address->zip_code ||
                            $existingAddress->city     !=  $address->city     ||
                            $existingAddress->country  !=  $address->country
                        ) {
                            $insertAddress = true;
                        }
                    } else {
                        // the contact has no address : create a new one and link the contact
                        // to it
                        $insertAddress = true;
                    }

                    if($insertAddress && $address->save()) {
                        $contact->link('address', $address);    
                    }
                }

                $records['L' . $offset] = [
                    'data' => [
                        'message' => $message,
                        'record' => $normalizedRecord,
                        'contact' => [
                            'model' => $contact,
                            'validation' => $contact === null ? '(no model)' : $contact->getErrors()
                        ],
                        'address' => [
                            'model' => $address,
                            'validation' => $address === null ? '(no model)' :  $address->getErrors() 
                        ]
                    ]
                ];
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
        }

        return $this->render('result', [
            'errorMessage' => $errorMessage,
            'records'      => $records
        ]);
    }

    private function normalizeRecord($record)
    {
        unset($record['woman_name']);
        unset($record['license_num']);
        unset($record['license_cat']);
        unset($record['locality']);
        // normlize gender
        $record['gender'] = ($record['gender'] == 'Femme' ? '2' : '1');

        // input date is yyyy-mm-dd but contact attribute 'birthday' expects app format (dd/mm/yyyy)
        $record['birthday'] = DateHelper::toDateAppFormat($record['birthday']);
        return $record;
    }
}
