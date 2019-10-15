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

/**
 * Default controller for the `gymv` module
 */
class ProductController extends Controller
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
        $productInserted = [];
        $productUpdated  = [];
        $categoriesInserted = [];

        try {
            // define CSV reader properties -------------------------------------------------
            $csv = Reader::createFromStream(fopen($importFile, 'r'));
            $csv->setDelimiter(';');
            //$csv->setEnclosure('\'');
            $csv->setHeaderOffset(0);
            $csvRecords = $csv->getRecords(['JOUR', 'LIEUX', 'COURS_NUM','HEURES', 
                'COURS', 'RESPONSABLES','TELEPHONE','ANIMATEURS','CATEGORY',
                'VALUE', 'VALUE1', 'VALUE2'
            ]);
            $input_bom = $csv->getInputBOM();

            if ($input_bom === Reader::BOM_UTF16_LE || $input_bom === Reader::BOM_UTF16_BE) {
                CharsetConverter::addTo($csv, 'utf-16', 'utf-8');
            }

            // loop on all non empty CSV lines- ----------------------------------------------
            foreach ($csvRecords as $offset => $record) {
                $normalizedRecord = $this->normalizeRecord($record);

                // first start with categories
                if (!empty($normalizedRecord['CATEGORY'])) {
                    $categoryAttributes = [
                        'name' =>  $normalizedRecord['CATEGORY'],
                        'type' => \app\components\ModelRegistry::PRODUCT
                    ];
                    $categories = Category::findAll($categoryAttributes);
                    if( count($categories) == 0) {
                        $category = new Category($categoryAttributes);
                        $category->setScenario(Category::SCENARIO_INSERT);
                        $category->save();
                        $categoriesInserted[] = $category;
                    } elseif (count($categories) == 1 ) {
                        $category = $categories[0];
                    } else {
                        // we have more than one category that matched for this product
                        // we don't know which one to choose : skip this product
                        continue;
                    }
    
                    if (!isset($category->id)) {
                        continue;
                    }
                } else {
                    $category = null;
                }

                // let's process the product now. First check if we must INSERT or UPDATE a product
                $productName =  'cours ' . $normalizedRecord['COURS_NUM'] . ' - ' . $normalizedRecord['COURS'];
                $secondaryProductAttributes = [
                    'short_description' =>  $normalizedRecord['JOUR']
                                                . ' ' . $normalizedRecord['HEURES']
                                                . ' - ' . $normalizedRecord['LIEUX'],
                    'value'             => $normalizedRecord['VALUE'],
                    'category_id'       => ($category !== null ? $category->id : null)                        
                ];              
                $existingProduct = Product::findOne(['name' => $productName]);
                if ($existingProduct != null) {
                    // UPDATE new product
                    $existingProduct->setAttributes($secondaryProductAttributes);
                    $existingProduct->save();
                    $productUpdated[] = $existingProduct;
                } else {
                    // INSERT new product
                    $product = new Product();                    
                    $product->setAttributes($secondaryProductAttributes);
                    $product->name = $productName;
                    $product->save();
                    $productInserted[] = $product;
                }
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
        }

        return $this->render('result', [
            'errorMessage'       => $errorMessage,
            'productInserted'    => $productInserted,
            'productUpdated'     => $productUpdated,
            'categoriesInserted' => $categoriesInserted
        ]);
    }

    private function normalizeRecord($record)
    {
        // nothing to normalize
        return $record;

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
