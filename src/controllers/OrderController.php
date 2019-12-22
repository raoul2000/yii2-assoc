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
use app\components\SessionDateRange;
use app\components\helpers\DateHelper;

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
            'date-range' => [
                'class' => 'app\components\actions\DateRangeAction',
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
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            Order::find()
                ->andWhereValidInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
        );
        $totalValue = $dataProvider->query->sum('value'); // compute total Value

        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            Order::find()
                ->andWhereValidInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
                ->with('transactions')
        );

        //$dataProvider->pagination->pageSize = 3;

        $productIndex = Product::getNameIndex();
        $contactIndex = Contact::getNameIndex();

        if (\app\components\widgets\DownloadDataGrid::isDownloadRequest()) {
            // request for downloading data grid
            $exporter = new \yii2tech\csvgrid\CsvGrid(
                [
                    'dataProvider' => new \yii\data\ActiveDataProvider([
                        'query' => $dataProvider->query,
                        'pagination' => [
                            'pageSize' => 100, // export batch size
                        ],
                    ]),
                    'columns' => [
                        ['attribute' => 'product', 'value' => function($model) use($productIndex) {
                            return $productIndex[$model->product_id];
                        }],
                        ['attribute' => 'provider', 'value' => function($model) use($contactIndex) {
                            return $contactIndex[$model->from_contact_id];
                        }],
                        ['attribute' => 'consumer', 'value' => function($model) use($contactIndex) {
                            return $contactIndex[$model->to_contact_id];
                        }],
                        ['attribute' => 'value'],
                        ['attribute' => 'valid_date_start'],
                        ['attribute' => 'valid_date_end'],
                    ]
                ]
            );
            \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            return $exporter->export()->send('orders.csv');

        } else {
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'products' => $productIndex,
                'contacts' => $contactIndex,
                'totalValue' => $totalValue
            ]);
        }
        
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
     * If a transaction_id is provided, the newly created order(s) is/are linked to the transaction and the browser
     * is redirected to the transaction 'view' page.
     * If a contact_id is provided, it is used as the benefeciary for the order(s) created
     *
     * @param $transaction_id ID of the transaction to link to the newly created order
     * @param $to_contact_id ID of the contact beneficiary fo this order
     * @param $redirect_url the url to redirect the browser to on success
     * @return mixed
     */
    public function actionCreate($transaction_id = null, $to_contact_id = null, $redirect_url = null)
    {
        $model = new Order();
        $transaction = null;
        $toContact = null;

        // do we have a transaction_id ? if yes and it is valid, it will be linked to the
        // newly created order(s)
        if ($transaction_id != null) {
            $transaction = Transaction::findOne($transaction_id);
            if ($transaction === null) {
                throw new NotFoundHttpException('The requested transaction does not exist.');
            }
        }

        // do we have a to_contact_id ? if yes and valid, it is used as beneficiary contact
        // for the newly created order
        if ($to_contact_id != null) {
            $toContact = Contact::findOne($to_contact_id);
            if ($toContact === null) {
                throw new NotFoundHttpException('The requested contact does not exist.');
            } else {
                $model->to_contact_id = $toContact->id;  // assign beneficiary contact
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $lazyUpdate = [];
            // if configured and if the current order has no value, try to use related product value
            if (!empty($model->product->value)
                && $model->value == 0
                && Yii::$app->configManager->getItemValue('order.create.setProductValue') == true)
            {
                $lazyUpdate['value'] = $model->product->value;
            }

            // RULE : if order has no valid date and product has, use the ones from product
            if (empty($model->valid_date_start) && empty($model->valid_date_end)
                && ( !empty($model->product->valid_date_start) || !empty($model->product->valid_date_end)))
            {
                $lazyUpdate['valid_date_start'] = DateHelper::toDateDbFormat($model->product->valid_date_start);
                $lazyUpdate['valid_date_end']  = DateHelper::toDateDbFormat($model->product->valid_date_end);
            }

            if (count($lazyUpdate) != 0) {
                $model->updateAttributes($lazyUpdate);
            }

            if ($transaction != null) {
                $model->linkToTransaction($transaction);
            }
            return $this->redirect(($redirect_url ? $redirect_url : ['view', 'id' => $model->id]));
        }

        if ($model->to_contact_id == null && $transaction !== null) {
            $model->to_contact_id = $transaction->fromAccount->contact_id;
        }

        // populate validity date range fields
        if (
                Yii::$app->configManager->getItemValue('order.create.setDefaultValidity')
                && empty($model->valid_date_start)
                && empty($model->valid_date_end)
        ) {
            $model->valid_date_start =  DateHelper::toDateAppFormat(SessionDateRange::getStart());
            $model->valid_date_end  =  DateHelper::toDateAppFormat(SessionDateRange::getEnd());
        }

        // render view
        return $this->render('create', [
            'model' => $model,
            'products' => \app\models\Product::getNameIndex(),
            'contacts' => \app\models\Contact::getNameIndex(),
            'toContact' => $toContact,
            'transaction' => $transaction
        ]);
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
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
            'toContact' => null,
        ]);
    }
    /**
     * Link an order with a transaction.
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
            $order->linkToTransaction($transaction);
            return $this->redirect(['view', 'id' => $order->id]);
        }

        // prepare the form model holding filter values
        $transactionSearchModel = new TransactionSearch();

        // if no filter is applied, use the first account of the beneficiary to populate the from_account_id field
        if (array_key_exists('TransactionSearch', Yii::$app->request->getQueryParams()) == false) {
            $transactionSearchModel->from_account_id = $order->toContact->bankAccounts[0]->id;
        }

        // search only transaction not already linked to this order
        $linkedTransactionIds = [];
        foreach ($order->transactions as $transaction) {
            $linkedTransactionIds[] = $transaction->id;
        }

        // apply user enetered filter values and built-in conditions
        $transactionDataProvider = $transactionSearchModel->search(
            Yii::$app->request->queryParams,
            Transaction::find()
                ->dateInRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
                ->andWhere([ 'not in', 'id', $linkedTransactionIds])
        );

        return $this->render('link-transaction', [
            'order' => $order,
            'transactionSearchModel' => $transactionSearchModel,
            'transactionDataProvider' => $transactionDataProvider,
            'bankAccounts' => BankAccount::getNameIndex()
        ]);
    }

    /**
     * Remove relation between an order and a transaction.
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
        $order->unlinkFromTransaction($transaction);
        return $this->redirect($redirect_url);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
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
