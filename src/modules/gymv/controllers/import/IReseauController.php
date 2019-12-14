<?php

namespace app\modules\gymv\controllers\import;

use Yii;
use yii\web\Controller;
use League\Csv\Exception;
use League\Csv\Reader;
use app\models\Contact;
use app\models\BankAccount;
use app\models\Address;
use app\models\Order;
use app\modules\gymv\models\UploadForm;
use yii\web\UploadedFile;
use \app\components\helpers\DateHelper;
use \app\components\SessionDateRange;


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
            $csv = Reader::createFromStream(fopen($importFile, 'r'));
            $csv->setDelimiter(';');
            $csv->setEnclosure('"');
            $csv->setHeaderOffset(0);
            $csvRecords = $csv->getRecords(['name', 'woman_name', 'firstname','gender', 'birthday', 'license_num',
            'license_cat','residence','locality','street','zip','city','country','phone', 'mobile', 'email',
            'certificate','type_adhesion', 'num_bdc']);

            foreach ($csvRecords as $offset => $record) {
                $message = [];
                $contact = $address = $licenseOrder = null;
                $contactAvailable = false;

                $normalizedRecord = $this->normalizeRecord($record);

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

                if ($contact) { /////////////////// address /////////////////////////////////////////////////

                    // We have a contact record saved in DB, now let's work on the related address
                    // for this contact
                    $addressAttributes = [
                        'line_1'   => $normalizedRecord['street'],
                        'line_2'   => $normalizedRecord['residence'],
                        'zip_code' => $normalizedRecord['zip'],
                        'city'     => $normalizedRecord['city'],
                        'country'  => $normalizedRecord['country']
                    ];
                    // first : is this address already exists in DB ? 
                    $existingAddress = Address::find()
                        ->where($addressAttributes)
                        ->one();

                    if ($existingAddress !== null) {
                        // the same address exists : link contact to this address
                        $contact->link('address', $existingAddress);    
                        $message[] = 'link with existing address (id=' . $existingAddress->id . ')';
                    } else {
                        // we have no such address in DB : insert it and link contact to it
                        $address = new Address($addressAttributes);
                        if($address->save()) {
                            $contact->link('address', $address);    
                            $message[] = 'insert address';
                        } else {
                            $message[] = '❌ address validation failed';
                        }
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
                        $licenseOrder->description      = 'license n° ' . $normalizedRecord['license_num']
                            . ( \strlen($normalizedRecord['num_bdc']) !== 0 
                                ? '- BDC N° ' . $normalizedRecord['num_bdc']
                                : ''
                            );
                        
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
                            //'model' => $contact,
                            'validation' => $contact === null ? '(no model)' : $contact->getErrors()
                        ],
                        'address' => [
                            //'model' => $address,
                            'validation' => $address === null ? '(no model)' :  $address->getErrors() 
                        ],
                        'licenseOrder' => [
                            //'model' => $licenseOrder,
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
        unset($record['woman_name']);
        //unset($record['license_num']);
        //unset($record['license_cat']);
        unset($record['locality']);

        // all strings are converted to lower case
        $record = array_map(function($colValue) {
            if(is_string($colValue)) {
                return \mb_strtolower(trim($colValue));
            } else {
                return trim($colValue);
            }
        }, $record);

        // normlize gender (Homme => 1, Femme => 2)
        $record['gender'] = ($record['gender'] == 'femme' ? '2' : '1');

        //$record['phone'] = $this->normalizePhone($record['phone']);
        //$record['mobile'] = $this->normalizePhone($record['mobile']);


        // input date is yyyy-mm-dd but contact attribute 'birthday' expects app format (dd/mm/yyyy)
        //$record['birthday'] = DateHelper::toDateAppFormat($record['birthday']);
        return $record;
    }
    private function normalizePhone($input)
    {
        if( \preg_match('/^\'(.*)\'$/m', $input, $matches) === 1) {
            return $matches[1];
        } else {
            return null;
        }
    }
}
