<?php

namespace app\controllers;

use Yii;
use app\models\Product;
use app\models\ProductSearch;
use app\models\Category;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\SessionDateRange;
use app\components\helpers\DateHelper;
use app\components\ModelRegistry;

/**
 * ProductController implements the CRUD actions for Product model.
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
                'class' => VerbFilter::className(),
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

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider
            ->query
            ->andWhereValidInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd());

        $categories = Category::getCategories(
            ModelRegistry::PRODUCT 
        );
        if (\app\components\widgets\DownloadDataGrid::isDownloadRequest()) {
            $exporter = new \yii2tech\csvgrid\CsvGrid(
                [
                    'dataProvider' => new \yii\data\ActiveDataProvider([
                        'query' => $dataProvider->query,
                        'pagination' => [
                            'pageSize' => 100, // export batch size
                        ],
                    ]),
                    'columns' => [
                        ['attribute' => 'name'],
                        ['attribute' => 'value'],
                        [
                            'attribute' => 'category', 
                            'label' => \Yii::t('app', 'category'),
                            'value' => function($model) use($categories) {
                                return $model->category_id != null 
                                ? $categories[$model->category_id]
                                : null;          
                            }],
                        ['attribute' => 'short_description'],
                        ['attribute' => 'description'],
                        ['attribute' => 'valid_date_start'],
                        ['attribute' => 'valid_date_end'],
                    ]
                ]
            );
            \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            return $exporter->export()->send('products.csv');
        } else {
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'categories' => $categories
            ]);
        }
    }

    /**
     * Displays a single Product model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Product();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        // populate validity date range fields
        if (
            Yii::$app->configManager->getItemValue('product.create.setDefaultValidity')
            && empty($model->valid_date_start)
            && empty($model->valid_date_end)
        ) {
            $model->valid_date_start = DateHelper::toDateAppFormat(SessionDateRange::getStart());
            $model->valid_date_end =  DateHelper::toDateAppFormat(SessionDateRange::getEnd());
        }
        
        // render view
        return $this->render('create', [
            'model' => $model,
            'categories' => Category::getCategories(
                ModelRegistry::PRODUCT 
            )
        ]);
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'categories' => Category::getCategories(
                ModelRegistry::PRODUCT 
            )
        ]);
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
