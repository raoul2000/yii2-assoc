<?php

namespace app\modules\gymv\controllers\import;

use Yii;
use yii\web\Controller;
use League\Csv\Exception;
use League\Csv\Reader;
use app\models\Category;
use app\models\Product;
use app\modules\gymv\models\UploadForm;
use yii\web\UploadedFile;

class CourseController extends Controller
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
                Yii::$app->session['import_course'] = $uploadFilepath;
                return $this->redirect(['import-csv']);
            }
        }

        return $this->render('index', ['model' => $model]);
    }

    public function actionImportCsv()
    {
        if (!Yii::$app->session->has('import_course')) {
            Yii::$app->session->setFlash('warning', 'No file uploaded');
            return $this->redirect(['index']);
        }
        $importFile = Yii::$app->session['import_course'];
        
        $errorMessage = null;
        $records = [];
        $courseNumber = null;
        // Product for the current Course 
        $courseProduct = null; 
        try {
            $csv = Reader::createFromStream(fopen($importFile, 'r'));
            $csv->setDelimiter(',');
            $csv->setEnclosure('\'');
            $csv->setHeaderOffset(0);
            $csvRecords = $csv->getRecords(['name', 'firstname']);

            foreach ($csvRecords as $offset => $record) {
                $message = [];
                $contact = null;
                $order = null;

                $normalizedRecord = $this->normalizeRecord($record);

                // identify the course number and load the corresponding product
                if (
                    $courseProduct === null 
                    && \strlen($normalizedRecord['name']) === 0 
                    && preg_match('/^cours ([0-9]+)$/',$normalizedRecord['firstname'], $matches) === 1 
                ) {
                    $courseNumber = $matches[1];
                    $message[] = 'cours n° ' . $courseNumber;

                    // load the corresponding product model
                    $results = Product::find()
                        ->where(['LIKE', 'name', "%cours $courseNumber "])
                        ->all();
                    if( count($results) === 1) {
                        $courseProduct = $results[0];
                    }
                    continue;
                } else {
                    continue;
                }
                
                continue;

                $contactAttributes = [
                    'is_natural_person' => true,
                    'name'      => $normalizedRecord['name'],
                    'firstname' => $normalizedRecord['firstname'],
                    'gender'    => $normalizedRecord['gender'],
                    'birthday'  => $normalizedRecord['birthday'],
                ];

                ///////////////////////////// contact //////////////////////////////////////////

                $contact = Contact::find()
                    ->where($contactAttributes)
                    ->one();

                if ( $contact ) {
                    // contact found : skip this contact
                    $message[] = 'contact exist (skip) : ' . $contact->fullname;
                } else {
                    
                    // contact does not exist in DB : insert it and create
                    // its default bank account
                    $contact = new Contact($contactAttributes);
                    $contact->setAttributes([
                        'email'    => $normalizedRecord['email'],
                        'birthday' => DateHelper::toDateAppFormat($record['birthday']),
                        'phone_1'  => $normalizedRecord['phone'],
                        'phone_2'  => $normalizedRecord['mobile']
                    ]);
                    
                    if ($contact->save()) {
                        $bankAccount = new BankAccount();
                        $bankAccount->contact_id = $contact->id;
                        $bankAccount->name = '';
                        $bankAccount->save(false);
                        $contactAvailable = true;
                        $message[] = 'contact inserted';
                    } else {
                        $message[] = '❌ contact validation failed';
                    }
                }
                if ($contact) { ///////////////////////// license ////////////////////////////////////////////

                    // license : The license is represented as an order from the license provider
                    // and to the contact.

                    // does this contact already has a license ?
                    $orderAttribute = [
                        'from_contact_id' => Yii::$app->params['contact.licence.provider'],
                        'to_contact_id'   => $contact->id,
                        'product_id'      => $normalizedRecord['license_cat'] === 'adulte avec assurance'
                            ? Yii::$app->params['registration.product.license_adulte']
                            : Yii::$app->params['registration.product.license_enfant']
                    ];

                    $hasLicenceOrder = Order::find()
                        ->validInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
                        ->where($orderAttribute)
                        ->exists();

                    if(! $hasLicenceOrder) {
                        // this contact has no registered licence : create the order now
                        $licenseOrder = new Order($orderAttribute);
                        $licenseOrder->valid_date_start =  DateHelper::toDateAppFormat(SessionDateRange::getStart());
                        $licenseOrder->valid_date_end   =  DateHelper::toDateAppFormat(SessionDateRange::getEnd());
                        $licenseOrder->description      = 'license n° ' . $normalizedRecord['license_num'];
                        
                        if( $licenseOrder->validate()) {
                            $licenseOrder->save();
                            $message[] = 'Insert License order';
                        } else {
                            $message[] = '❌ order validation failed';
                        }
                    } else {
                        $message[] = 'License order already exists in this period';
                    }
                }

                //////////////////////// final report //////////////////////////////////////////////////////

                $records['L' . $offset] = [
                    'data' => [
                        'message' => $message,
                        'record' => $normalizedRecord,
                        'contact' => [
                            'model' => $contact,
                            'validation' => $contact === null ? '(no model)' : $contact->getErrors()
                        ],
                        'courseOrder' => [
                            'model' => $licenseOrder,
                            'validation' => $licenseOrder === null ? '(no model)' :  $licenseOrder->getErrors() 
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
        $record = array_map(function($colValue) {
            if(is_string($colValue)) {
                return \strtolower(trim($colValue));
            } else {
                return $colValue;
            }
        }, $record);

        // nothing to normalize
        return $record;
    }
}
