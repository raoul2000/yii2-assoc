<?php

namespace app\controllers;

use Yii;
use app\models\Transaction;
use app\models\Order;
use app\models\Product;
use app\models\Contact;
use app\models\OrderSearch;
use app\models\TransactionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
{
    public function actions()
    {
        return [
            // declares "error" action using a class name
            'delete' => [
                'class' => 'app\components\actions\orders\DeleteAction',
            ],
        ];
    }
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
        ];
    }

    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'products' => Product::getNameIndex(),
            'contacts' => Contact::getNameIndex()
        ]);
    }

    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $order = $this->findModel($id);
        $transactionSearchModel = new TransactionSearch();
        $transactionDataProvider = $transactionSearchModel->search(Yii::$app->request->queryParams, $order->getTransactions());

        return $this->render('view', [
            'model' => $order,
            'transactionSearchModel' => $transactionSearchModel,
            'transactionDataProvider' => $transactionDataProvider,
            'products' => Product::getNameIndex(),
            'contacts' => Contact::getNameIndex()
        ]);
    }

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $transaction_id ID of the transaction to link to the newly created order
     * @return mixed
     */
    public function actionCreate($transaction_id = null)
    {
        $model = new Order();
        $transaction = null;
        if( $transaction_id != null ) {
            $transaction = Transaction::findOne($transaction_id);
            if( $transaction === null ) {
                throw new NotFoundHttpException('The requested transaction does not exist.');
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if( $transaction != null ) {
                $model->link('transactions', $transaction);
                return $this->redirect(['transaction/view', 'id' => $transaction_id]);    
            } else {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        if ( $model->contact_id == null && $transaction !== null) {
            $model->contact_id = $transaction->fromAccount->contact_id;
        }

        return $this->render('create', [
            'model' => $model,
            'products' => \app\models\Product::getNameIndex(),
            'contacts' => \app\models\Contact::getNameIndex(),
            'transaction' => $transaction
        ]);
    }

    /**
     * Updates an existing Order model.
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
            'products' => Product::getNameIndex(),
            'contacts' => Contact::getNameIndex(),
        ]);
    }

    public function actionLinkTransaction($id, $transaction_id = null)
    {
        $order = $this->findModel($id);
        if (isset($transaction_id)) {
            $transaction = Transaction::findOne($transaction_id);
            if (!isset($transaction)) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
            $order->link('transactions', $transaction);
            return $this->redirect(['view', 'id' => $order->id]);
        }        

        $transactionSearchModel = new TransactionSearch();
        $transactionDataProvider = $transactionSearchModel->search(Yii::$app->request->queryParams);
        // search only transaction not already linked to this order
        $linkedTransactionIds = [];
        foreach($order->transactions as $transaction) {
            $linkedTransactionIds[] = $transaction->id;
        }
        $transactionDataProvider->query->andWhere([ 'not in', 'id', $linkedTransactionIds]);

        return $this->render('link-transaction', [
            'order' => $order,
            'transactionSearchModel' => $transactionSearchModel,
            'transactionDataProvider' => $transactionDataProvider,
        ]);        
    }
    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
