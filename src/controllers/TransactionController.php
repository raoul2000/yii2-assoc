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
use app\models\Attachment;
use app\components\SessionDateRange;
use app\components\SessionContact;
use app\models\Category;
use app\components\ModelRegistry;

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
            'date-range' => [
                'class' => 'app\components\actions\DateRangeAction',
            ],
            'ajax-link-orders' => [
                'class' => 'app\components\actions\transactions\AjaxLinkOrdersAction'
            ],

            // attachments Actions /////////////////////////////////////////
            
            'download-attachment' => [
                'class' => 'app\components\actions\attachments\DownloadAction',
            ],
            'preview-attachment' => [
                'class' => 'app\components\actions\attachments\PreviewAction',
            ],
            'delete-attachment' => [
                'class' => 'app\components\actions\attachments\DeleteAction',
            ],
            'create-attachment' => [
                'class' => 'app\components\actions\attachments\CreateAction',
            ],
            'update-attachment' => [
                'class' => 'app\components\actions\attachments\UpdateAction',
            ],

            // tags Actions /////////////////////////////////////////

            'query-tags' => [
                'class' => 'app\components\actions\TagListAction',
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
     * Link a transaction with one or more existing order
     * @param $id transaction Id
     */
    public function actionLinkOrder($id)
    {
        $transaction = $this->findModel($id);

        // prepare the form model holding filter values
        $orderSearchModel = new OrderSearch();

        // if no filter is applied, use the first account of the beneficiary to populate the from_account_id fiel
        /*
        if (array_key_exists('OrderSearch', Yii::$app->request->getQueryParams()) == false) {
            $orderSearchModel->contact_id = $transaction->fromAccount->contact_id;
        }*/
        // apply user enetered filter values
        $orderDataProvider = $orderSearchModel->search(
            Yii::$app->request->queryParams,
            Order::find()
                ->andWhereValidInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
        );
        
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
     * Unlink a transaction from an order
     *
     * @param int $id the id of the transaction
     * @param int $order_id the id of the order to unlink
     * @param string $redirect_url the url to redirect the browser to
     * @return mixed
     */
    public function actionUnlinkOrder($id, $order_id, $redirect_url)
    {
        $transaction = $this->findModel($id);
        $order = Order::findOne($order_id);
        if (!isset($order)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $transaction->unlinkFromOrder($order);
        return $this->redirect($redirect_url);
    }
    /**
     * Lists all Transaction models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TransactionSearch();
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            Transaction::find()
                ->dateInRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
        );
        
        // compute total Value on the current grid rows
        $totalValue = $dataProvider->query->sum('value');

        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            Transaction::find()
                ->dateInRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
                ->with('orders')
        );

        // apply tag search condition if tag values have been submitted
        $tagValues = Yii::$app->request->get('tagValues');
        if (!empty($tagValues)) {
            $dataProvider
                ->query
                ->anyTagValues($tagValues);      
        }

        $categories = Category::getCategories(
            ModelRegistry::TRANSACTION 
        );

        $bankAccounts = BankAccount::getNameIndex();
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
                        ['attribute' => 'id'],
                        ['attribute' => 'sender account', 'value' => function($model) use ($bankAccounts) {
                            return $bankAccounts[$model->from_account_id];
                        }],
                        ['attribute' => 'recipient account', 'value' => function($model) use ($bankAccounts) {
                            return $bankAccounts[$model->to_account_id];
                        }],
                        ['attribute' => 'value'],
                        ['attribute' => 'reference_date'],
                        ['attribute' => 'code'],
                        ['attribute' => 'type'],   
                        ['attribute' => 'description'],   
                        ['attribute' => 'is_verified', 'format' => 'boolean'],
                        /*
                        ['attribute' => 'orders_value_total'],
                        ['attribute' => 'orderValuesDiff'],
                        */
                    ]
                ]
            );
            \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            return $exporter->export()->send('transactions.csv');

        } else {
            return $this->render('index', [
                'searchModel'  => $searchModel,
                'dataProvider' => $dataProvider,
                'bankAccounts' => $bankAccounts,
                'tagValues'    => $tagValues,
                'categories'   => $categories,
                'totalValue'   => $totalValue
            ]);
        }

    }
    /**
     * Displays a single Transaction model.
     * The view also displays a grid of all related orders and attachements.
     *
     * @param integer $id
     * @param string $tab active tab name
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $tab = 'orders')
    {
        $transaction = $this->findModel($id);
        
        switch ($tab) {
            case 'orders' :
                $orderSearchModel = new OrderSearch();
                $orderDataProvider = $orderSearchModel->search(
                    Yii::$app->request->queryParams,
                    $transaction->getOrders()
                );
        
                return $this->render('view', [
                    'model' => $transaction,
                    'tab' => $tab,
                    'tabContent' => $this->renderPartial('_tab-orders', [
                        'model' => $transaction,
                        'orderSearchModel' => $orderSearchModel,
                        'orderDataProvider' => $orderDataProvider,
                        'products' => Product::getNameIndex(),
                        'contacts' => Contact::getNameIndex()
                    ]),
                ]);
                break;

            case 'attachment' :
                return $this->render('view', [
                    'model' => $transaction,
                    'tab' => $tab,
                    'tabContent' => $this->renderPartial('/common/_tab-attachment', [
                        'model' => $transaction,
                    ])
                ]);
                break;

            default:
                return $this->redirect(['view', 'id' => $transaction->id, 'tab' => 'orders']);
                break;
        }
    }

    /**
     * Creates a new Transaction.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @param $order_id ID of the order to link to the newly created transaction
     * @return mixed
     */
    public function actionCreate($order_id = null, $from_account_id = null, $to_account_id = null)
    {
        $model = Transaction::create();

        $order = null;
        if ($order_id != null) {
            $order = Order::findOne($order_id);
            if ($order === null) {
                throw new NotFoundHttpException('The requested order does not exist.');
            }
        }

        if ($model->load(Yii::$app->request->post())) {
            // a new category has been entered by user : we must create a new Category entry
            if (!empty($model->category_id) && !is_numeric($model->category_id)) {
                $category = new Category();
                $category->setScenario(Category::SCENARIO_INSERT);
                $category->setAttributes([
                    //'contact_id' => SessionContact::getContactId(),   // categories are not private anymore
                    'type' => ModelRegistry::TRANSACTION,
                    'name' => $model->category_id
                ]);
                if ($category->save()) {
                    $model->category_id = $category->id;
                } else {
                    throw new \yii\web\ServerErrorHttpException('Failed to save new category');
                }
            }

            if ($model->save()) {
                if ($order !== null) {
                    $model->linkToOrder($order);
                }
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
     
        if ($from_account_id != null) {
            $model->from_account_id = $from_account_id;
        }

        if ($to_account_id != null) {
            $model->to_account_id = $to_account_id;
        }

        if ($order !== null) {
            // try to guess the source bank account if not provided
            // RULE : Use the first bank account belonging to the contact referenced
            // as beneficiary in the order instance
            if ($model->from_account_id == null && count($order->toContact->bankAccounts) > 0) {
                $model->from_account_id = $order->toContact->bankAccounts[0]->id;
            }

            // try to guess the transaction value
            // RULE : use the order's value as transaction value
            if ($model->value == null) {
                $model->value = $order->value;
            }
        }

        return $this->render('create', [
            'model' => $model,
            'bankAccounts' => [[ '' => 'null']] +  BankAccount::getNameIndex(),
            'products' => isset($order) ? null : Product::getNameIndex(),
            'order' => $order,
            'categories' => [[ '' => 'null']] + Category::getCategories(
                ModelRegistry::TRANSACTION  // categories are not private anymore
            )
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

        if ($model->load(Yii::$app->request->post())) {
            // a new category has been entered by user : we must create a new Category entry
            if (!empty($model->category_id) && !is_numeric($model->category_id)) {
                $category = new Category();
                $category->setScenario(Category::SCENARIO_INSERT);
                $category->setAttributes([
                    //'contact_id' => SessionContact::getContactId(),   // categories are not private anymore
                    'type' => ModelRegistry::TRANSACTION,
                    'name' => $model->category_id
                ]);
                if ($category->save()) {
                    $model->category_id = $category->id;
                } else {
                    throw new \yii\web\ServerErrorHttpException('Failed to save new category');
                }
            }

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        $categories = Category::getCategories(
            ModelRegistry::TRANSACTION  // categories are not private anymore
        );
        
        // the category Id could have been set using another session contact and then, the category
        // does not exist for the current user :  inject the foreign category in the category list
        // in order to correctly populate the dropdown list
        if (isset($model->category_id) && !array_key_exists($model->category_id, $categories)) {
            $categories[$model->category_id] =  $model->category->name;
        }
        return $this->render('update', [
            'model' => $model,
            'bankAccounts' => [[ '' => 'null']] + BankAccount::getNameIndex(),
            'products' => isset($order) ? null : Product::getNameIndex(),
            'categories' =>[[ '' => 'null']] +  $categories
        ]);
    }

    /**
     * Display all transactions and orders reltaed with each other.
     *
     * @param string $id transaction id
     * @return void
     */
    public function actionViewComplete($id)
    {
        /**
         * Insert transactions ofr this order into the transaction array
         * if not already present
         */
        $addTransactions = function ($order, $transactions) {
            $transactionAdded = false;
            foreach ($order->transactions as $transaction) {
                $key = 'id:'+$transaction->id;
                if ( ! array_key_exists($key, $transactions)) {
                    $transactionAdded = true;
                    $transactions[$key] = $transaction;
                }
            }
            return [$transactionAdded, $transactions];
        };
        /**
         * insert orders for this transaction into an array (avoiding duplicate)
         */
        $addOrders = function ($transaction, $orders) {
            $orderAdded = false;
            foreach ($transaction->orders as $order) {
                $key = 'id:'+$order->id;
                if ( ! array_key_exists($key, $orders)) {
                    $orderAdded = true;
                    $orders[$key] = $order;
                }
            }
            return [$orderAdded, $orders];
        };

        /* ----------------------------------------*/
        $transactionModel = $this->findModel($id);

        $orders = [];
        $transactions = [];
        $transactions['id:'+$transactionModel->id] = $transactionModel;

        $transactionAdded = true;
        while ($transactionAdded) {
            foreach ($transactions as $transaction) {
                list($added, $orders) = $addOrders($transaction, $orders);
            }
            $transactionAdded = false;
            foreach ($orders as $order) {
                list($added, $transactions) = $addTransactions($order, $transactions);
                if ($added) {
                    $transactionAdded = true;
                }
            }
        }

        // comput transaction and order values sum
        $valueReducer = function ($acc, $current) {
            return $acc + ( is_numeric($current->value) ? $current->value : 0);
        };

        $transactionValueSum = array_reduce($transactions, $valueReducer);
        $orderValueSum = array_reduce($orders, $valueReducer);

        return $this->render('view-complete', [
            'transactions' => $transactions,
            'orders' => $orders,
            'transactionValueSum' => array_reduce($transactions, $valueReducer),
            'orderValueSum' => array_reduce($orders, $valueReducer),
            'bankAccounts' => \app\models\BankAccount::getNameIndex(),
            'products' => \app\models\Product::getNameIndex(),
            'contacts' => \app\models\Contact::getNameIndex(),
            'transactionType' => \app\components\Constant::getTransactionTypes(),
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
    public function findModel($id)
    {
        if (($model = Transaction::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
