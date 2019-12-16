<?php

namespace app\modules\gymv\controllers\import;

use Yii;
use yii\web\Controller;
use League\Csv\Exception;
use League\Csv\Reader;
use app\models\Contact;
use app\models\Order;
use app\models\Product;
use app\modules\gymv\models\UploadForm;
use yii\web\UploadedFile;
use \app\components\SessionContact;
use \app\components\SessionDateRange;
use \app\components\helpers\DateHelper;

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
        // Product for the current Course Map<course number, Product Model>
        $products = []; 
        $courseProduct = null; 
        try {
            $csv = Reader::createFromStream(fopen($importFile, 'r'));
            $csv->setDelimiter(',');
            $csv->setEnclosure('\'');
            $csv->setHeaderOffset(0);
            $csvRecords = $csv->getRecords(['name', 'firstname', 'cour_num']);

            foreach ($csvRecords as $offset => $record) {
                $message = [];
                $contact = null;
                $order = null;

                $normalizedRecord = $this->normalizeRecord($record);

                // identify the course number and load the corresponding product

                $courseNumber = $normalizedRecord['cour_num'];

                if ( ! array_key_exists($courseNumber, $products) ) { // product ////////////////////////////////
                    // course product not store in cache : find it
                    $results = Product::find()
                        ->where(['LIKE', 'name', "cours $courseNumber -"])
                        ->all();
                    if( count($results) === 1) {
                        // found ! cache for later use
                        $products[$courseNumber] = $results[0];
                    } else {
                        // mark as not found in the cache entry
                        $products[$courseNumber] = false;
                        $message[] = '❌ product not found for course ' . $courseNumber;
                    }
                }

                if( $products[$courseNumber] !== false) {
                    $courseProduct = $products[$courseNumber];
                } else {
                    $message[] = '❌ product stil missing course = ' . $courseNumber;
                    $courseProduct = null;
                }

                if($courseProduct) { // contact //////////////////////////////////////////////////

                    // exact match
                    /*
                    $results = Contact::find()
                        ->andWhere(['LIKE', 'name', $normalizedRecord['name']])
                        ->andWhere(['LIKE', 'firstname', $normalizedRecord['firstname']])
                        ->all();
                    */

                    // fuzzy search
                    $results = Contact::find()
                        ->andWhere(['LIKE', 'name', $normalizedRecord['name']])
                        ->andWhere([
                            'or like', 
                            'firstname', 
                            $this->createVariant($normalizedRecord['firstname'])
                        ])
                        ->all();

                    if (count($results) === 1) {
                        $contact = $results[0];
                    } else {
                        $message[] = '❌ contact not found : ' . $normalizedRecord['name'] . ', ' . $normalizedRecord['firstname'];
                        $contact = null;
                    }
                }

                if ($courseProduct && $contact) { ///////////////////////// order - course /////////////////

                    // course is represented as an order for the course product, between the configured
                    // contact (current session contact) and the imported contact

                    // does this order already exists ?
                    $orderAttribute = [
                        'from_contact_id' => SessionContact::getContactId(),
                        'to_contact_id'   => $contact->id,
                        'product_id'      => $courseProduct->id
                    ];

                    $hasOrder = Order::find()
                        ->validInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
                        ->where($orderAttribute)
                        ->exists();

                    if(! $hasOrder) {
                        // create order for this course and this contatc
                        $order = new Order($orderAttribute);
                        $order->valid_date_start =  DateHelper::toDateAppFormat(SessionDateRange::getStart());
                        $order->valid_date_end   =  DateHelper::toDateAppFormat(SessionDateRange::getEnd());
                        
                        if( $order->validate()) {
                            $order->save();
                            $message[] = '✔️ Insert course order';
                        } else {
                            $message[] = '❌ order validation failed';
                        }
                    } else {
                        $message[] = 'ℹ️ course order already exists in this period';
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
                            'model' => $order,
                            'validation' => $order === null ? '(no model)' :  $order->getErrors() 
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
                return \mb_strtolower(trim($colValue));
            } else {
                return $colValue;
            }
        }, $record);

        return $record;
    }

    /**
     * Helper method that returns string variants from a given string
     *
     * @param string $value
     * @param boolean $insert when TRUE, the given value is added as the first item in the returned variants
     * When FALSE, only variants are returned
     * @return [string]
     */
    private function createVariant($value, $insert = true)
    {
        $variant = $insert ? [$value] : [];
        if(preg_match('/[ -]/',  $value) === 1) {
            // the value contains a separator : we must provide vazriant
            $rootStr = preg_replace('/[ -]+/', ' ', $value);
            $tokens = explode(' ', $rootStr);

            $variant[] = implode(' ',$tokens);
            $variant[] = implode('-',$tokens);
        } 
        return $variant;
    }
}
