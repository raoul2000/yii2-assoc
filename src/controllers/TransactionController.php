<?php

namespace app\controllers;

use Yii;
use app\models\BankAccount;
use app\models\Transaction;
use app\models\Product;
use app\models\Contact;
use app\models\Order;
use app\models\OrderSearch;
use app\models\TransactionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

/**
 * TransactionController implements the CRUD actions for Transaction model.
 */
class TransactionController extends Controller
{
    public function actions()
    {
        return [
            // declares "error" action using a class name
            'delete-order' => [
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
     * Link a transaction with one existing order
     * @param $id transaction Id
     * @param $order_id Id of the order to link to
     */
    public function actionLinkOrder($id, $order_id = null)
    {
        $transaction = $this->findModel($id);
        if (isset($order_id)) {
            $order = Order::findOne($order_id);
            if (!isset($order)) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
            $transaction->link('orders', $order);
            return $this->redirect(['view', 'id' => $transaction->id]);
        }

        // prepare the form model holding filter values
        $orderSearchModel = new OrderSearch();

        // if no filter is applied, use tge first account of the beneficiary to populate the from_account_id field
        if (array_key_exists('OrderSearch', Yii::$app->request->getQueryParams()) == false) {
            $orderSearchModel->contact_id = $transaction->fromAccount->contact_id;
        }
        // apply user enetered filter values
        $orderDataProvider = $orderSearchModel->search(Yii::$app->request->queryParams);
        
        // search only order not already linked to this transaction
        $linkedOrderIds = [];
        foreach ($transaction->orders as $order) {
            $linkedOrderIds[] = $order->id;
        }
        $orderDataProvider->query->andWhere([ 'not in', 'id', $linkedOrderIds]);

        return $this->render('link-order', [
            'transaction' => $transaction,
            'orderSearchModel' => $orderSearchModel,
            'orderDataProvider' => $orderDataProvider,
            'products' => Product::getNameIndex(),
            'contacts' => Contact::getNameIndex()
        ]);
    }

    /**
     * 
     */
    public function actionUnlinkOrder($id, $order_id, $redirect_url)
    {
        $transaction = $this->findModel($id);
        $order = Order::findOne($order_id);
        if (!isset($order)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $transaction->unlink('orders', $order, true);
        return $this->redirect($redirect_url);
    }
    /**
     * Lists all Transaction models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TransactionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Transaction::find()->with('orders'));
        //$dataProvider->query->with('orders');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'bankAccounts' => BankAccount::getNameIndex()
        ]);
    }


    /**
     * Displays a single Transaction model.
     * The view also displays a grid of all related orders
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $transaction = $this->findModel($id);
        $orderSearchModel = new OrderSearch();
        $orderDataProvider = $orderSearchModel->search(Yii::$app->request->queryParams, $transaction->getOrders());

        return $this->render('view', [
            'model' => $transaction,
            'orderSearchModel' => $orderSearchModel,
            'orderDataProvider' => $orderDataProvider,
            'products' => Product::getNameIndex(),
            'contacts' => Contact::getNameIndex()
        ]);
    }

    /**
     * Creates a new Transaction.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * When the form contains the field 'initial_product_id' it is used to automatically create an order.
     *
     * @param $order_id ID of the order to link to the newly created transaction
     * @return mixed
     */
    public function actionCreate($order_id = null)
    {
        $model = new Transaction();

        $order = null;
        if ($order_id != null) {
            $order = Order::findOne($order_id);
            if ($order === null) {
                throw new NotFoundHttpException('The requested order does not exist.');
            }
        }
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($order !== null) {
                $model->link('orders', $order);
            } else if (isset($model->initial_product_id)) {
                $initialProduct = \app\models\Product::findOne($model->initial_product_id);
                if ($initialProduct) {
                    // create the related orders
                    for ($iCount=0; $iCount < $model->initial_product_quantity; $iCount++) {
                        $order = new Order();
                        $order->setAttributes([
                            'product_id' => $initialProduct->id,
                            'contact_id' => $model->fromAccount->contact_id
                        ]);
                        $order->save();
                        $model->link('orders', $order);
                    }
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }
     
        if ($model->from_account_id == null && $order !== null) {
            $model->from_account_id = $order->contact->bankAccounts[0];
        }
        
        return $this->render('create', [
            'model' => $model,
            'bankAccounts' => BankAccount::getNameIndex(),
            'products' => Product::getNameIndex(),
            'order' => $order
        ]);
    }

    /**
     * Updates an existing Transaction model.
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
            'bankAccounts' => BankAccount::getNameIndex(),
            'products' => Product::getNameIndex()
        ]);
    }

    /**
     * Deletes an existing Transaction model.
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
     * Finds the Transaction model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Transaction the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Transaction::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
