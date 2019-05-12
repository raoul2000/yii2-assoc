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
                ->validInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
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
                ->with('orders')
        );

        SessionDateRange::applyDateRange($dataProvider, $searchModel);

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
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($order !== null) {
                $model->linkToOrder($order);
            }
            return $this->redirect(['view', 'id' => $model->id]);
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
            'bankAccounts' => BankAccount::getNameIndex(),
            'products' => isset($order) ? null : Product::getNameIndex(),
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
            'products' => isset($order) ? null : Product::getNameIndex(),
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
