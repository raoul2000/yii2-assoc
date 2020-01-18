<?php

namespace app\modules\gymv\controllers\export;

use Yii;
use yii\web\Controller;
use League\Csv\Exception;
use League\Csv\Reader;
use app\models\Category;
use app\models\Product;
use app\models\Contact;
use app\models\Order;
use app\modules\gymv\models\UploadForm;
use yii\web\UploadedFile;

/**
 * Default controller for the `gymv/import` 
 */
class HomeController extends Controller
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
        

    public function actionIndex($action = null)
    {
        if($action === 'export') {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            return $this->export()->send('export.csv');
        } else {
            return $this->render('index');
        }
    }

    // duplicate from member/HomeController
    protected function getQueryCourseIds()
    {
        return Product::find()
            ->select('id')
            ->from(['p' => Product::tableName()])
            ->where(['in', 'p.category_id', Yii::$app->params['courses_category_ids']])
            ->andWhere(\app\components\helpers\DateRangeHelper::buildConditionOnDateRange());
    }

    private function export()
    {
        $query = Order::find()
            ->where(['in', 'product_id', $this->getQueryCourseIds()])
            ->andWhere(\app\components\helpers\DateRangeHelper::buildConditionOnDateRange())
            ->with(['toContact', 'toContact.address', 'product', 'product.category']);

        $exporter = new \yii2tech\csvgrid\CsvGrid(
            [
                'dataProvider' => new \yii\data\ActiveDataProvider([
                    'query' => $query,
                    'pagination' => [
                        'pageSize' => 100, // export batch size
                    ],
                ]),
                'columns' => [
                    [
                        'label' => 'unité',
                        'value' => function ($model, $key, $index, $column)  {
                            return '1';
                        }
                    ],
                    [
                        'attribute' => 'toContact.id',
                        'label' => 'adhérent',
                        'value' => function ($model, $key, $index, $column)  {
                            return 'id-' . $model->toContact->id;
                        }
                    ],
                    ['attribute' => 'toContact.birthday'],
                    [
                        'label' => 'age',
                        'value' => function ($model, $key, $index, $column)  {
                            if (!empty($model->toContact->birthday)) {
                                // convert from App to DB format
                                $birthday = \app\components\helpers\DateHelper::toDateDbFormat($model->toContact->birthday);
                                return Yii::$app->formatter->asAge($birthday);
                            } else {
                                return null;
                            }
                        }
                    ],
                    ['attribute' => 'toContact.gender', 'format' => 'gender'],
                    ['attribute' => 'toContact.address.zip_code'],    
                    ['attribute' => 'product.name', 'label' => 'cours'],    
                    ['attribute' => 'product.category.name', 'label' => 'categorie'],    
                ],
                'csvFileConfig' => [
                    'cellDelimiter' => "\t",
                    'rowDelimiter' => "\n",
                    'enclosure' => '"',
                ],            
            ]
        );
        return $exporter->export();
    }
}
