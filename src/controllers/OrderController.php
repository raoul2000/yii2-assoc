<?php

namespace app\controllers;

use Yii;
use app\models\BankAccount;
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
            'create-date-range' => [
                'class' => 'app\components\actions\CreateDateRangeAction',
            ],
            'delete-date-range' => [
                'class' => 'app\components\actions\DeleteDateRangeAction',
            ]
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
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Order::find()->with('transactions'));
        //$dataProvider->pagination->pageSize = 3;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'products' => Product::getNameIndex(),
            'contacts' => Contact::getNameIndex()
        ]);
    }

    /**
     * Displays a single Order model.
     * @param integer $id the id of the model
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $order = $this->findModel($id);
        $transactionSearchModel = new TransactionSearch();
        $transactionDataProvider = $transactionSearchModel->search(
            Yii::$app->request->queryParams,             
            $order->getTransactions()
        );

        return $this->render('view', [
            'model' => $order,
            'transactionSearchModel' => $transactionSearchModel,
            'transactionDataProvider' => $transactionDataProvider,
            'products' => Product::getNameIndex(),
            'contacts' => Contact::getNameIndex(),
            'bankAccounts' => BankAccount::getNameIndex()
        ]);
    }

    /**
     * Creates a one or more Order models depending on the initial_quantity value.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * If a transaction_id is provided, the newly created order is link to the transaction and the browser
     * is redirected to the transaction 'view' page. 
     * @param $transaction_id ID of the transaction to link to the newly created order
     * @return mixed
     */
    public function actionCreate($transaction_id = null)
    {
        $model = new Order();
        $transaction = null;
        if ($transaction_id != null) {
            $transaction = Transaction::findOne($transaction_id);
            if ($transaction === null) {
                throw new NotFoundHttpException('The requested transaction does not exist.');
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $saveModel = null;
            for ($iCount=0; $iCount < $model->initial_quantity; $iCount++) {
                $saveModel = new Order([
                    'attributes' => $model->getAttributes()
                ]);
                $saveModel->save(false);

                if ($transaction != null) {
                    $saveModel->link('transactions', $transaction);
                }
            }

            if ($transaction != null) {
                return $this->redirect(['transaction/view', 'id' => $transaction_id]);
            } elseif ($model->initial_quantity == 1) {
                return $this->redirect(['view', 'id' => $saveModel->id]);
            } else {
                return $this->redirect(['index']);
            }
        }

        if ($model->contact_id == null && $transaction !== null) {
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
     * @param integer $id the id of the order model to update
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
    /**
     * Link the current order with a transaction.
     * This method displays a list of all transactions candidates to be linked
     * with this order
     *
     * @param int $id Id of the order
     * @param int $transaction_id id of the transaction to link to the order
     * @return mixed
     */
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

        // prepare the form model holding filter values
        $transactionSearchModel = new TransactionSearch();

        // if no filter is applied, use the first account of the beneficiary to populate the from_account_id field
        if (array_key_exists('TransactionSearch', Yii::$app->request->getQueryParams()) == false) {
            $transactionSearchModel->from_account_id = $order->contact->bankAccounts[0]->id;
        }
        // apply user enetered filter values
        $transactionDataProvider = $transactionSearchModel->search(Yii::$app->request->queryParams);

        // search only transaction not already linked to this order
        $linkedTransactionIds = [];
        foreach ($order->transactions as $transaction) {
            $linkedTransactionIds[] = $transaction->id;
        }
        $transactionDataProvider->query->andWhere([ 'not in', 'id', $linkedTransactionIds]);

        return $this->render('link-transaction', [
            'order' => $order,
            'transactionSearchModel' => $transactionSearchModel,
            'transactionDataProvider' => $transactionDataProvider,
            'bankAccounts' => BankAccount::getNameIndex()
        ]);
    }

    /**
     * Remove relation between and order and a transition
     *
     * @param int $id the id of the order
     * @param int $transaction_id the id of the transaction to unlink
     * @param string $redirect_url the url to redirect the browser to
     * @return mixed
     */
    public function actionUnlinkTransaction($id, $transaction_id, $redirect_url)
    {
        $order = $this->findModel($id);
        $transaction = Transaction::findOne($transaction_id);
        if (!isset($transaction)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $order->unlink('transactions', $transaction, true);
        return $this->redirect($redirect_url);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id the id of the order
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
