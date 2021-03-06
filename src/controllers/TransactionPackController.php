<?php

namespace app\controllers;

use Yii;
use app\models\TransactionPack;
use app\models\TransactionPackSearch;
use app\models\TransactionSearch;
use app\models\Transaction;
use app\models\BankAccount;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\SessionDateRange;

/**
 * TransactionPackController implements the CRUD actions for TransactionPack model.
 */
class TransactionPackController extends Controller
{
    public function actions()
    {
        return [
            'ajax-link-transactions' => [
                'class' => 'app\components\actions\transactionPack\AjaxLinkTransactionsAction'
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

    /**
     * Lists TransactionPack models.
     * If a date range is defined, only thoses models valid for the date range are displayed
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TransactionPackSearch();
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            TransactionPack::find()
                ->dateInRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
        );

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'bankAccounts' => BankAccount::getNameIndex()
        ]);
    }
    /**
     * Link a transaction pack with one or more existing transactions
     * @param $id transaction Id
     */
    public function actionLinkTransaction($id)
    {
        $transactionPack = $this->findModel($id);

        // prepare the form model holding filter values
        $transactionSearchModel = new TransactionSearch();

        // apply user enetered filter values
        $transactionDataProvider = $transactionSearchModel->search(
            Yii::$app->request->queryParams,
            Transaction::find()
                ->dateInRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
                ->andWhere(['transaction_pack_id' => null])
        );
        // compute total Value on the current grid rows
        $totalValue = $transactionDataProvider->query->sum('value');

        return $this->render('link-transaction', [
            'transactionPack' => $transactionPack,
            'transactionSearchModel' => $transactionSearchModel,
            'transactionDataProvider' => $transactionDataProvider,
            'bankAccounts' => BankAccount::getNameIndex(),
            'totalValue' => $totalValue
        ]);
    }
    /**
     * Unlink a transaction from this transaction pack
     *
     * @param int $id id of the transaction pack
     * @param int $transaction_id id of the transaction to unlink
     * @param string $redirect_url redirect url
     * @return void
     */
    public function actionUnlinkTransaction($id, $transaction_id, $redirect_url)
    {
        $transactionPack = $this->findModel($id);
        $transaction = Transaction::findOne($transaction_id);
        if (!isset($transaction)) {
            throw new NotFoundHttpException('The requested transaction does not exist.');
        }
        $transactionPack->unlink('transactions', $transaction);
        return $this->redirect($redirect_url);
    }
    /**
     * Displays a single TransactionPack model with all its linked transactions and attachement
     * presented in tabs
     *
     * @param integer $id
     * @param string $tab
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $tab = 'transaction')
    {
        $model =  $this->findModel($id);
        switch ($tab) {
            case 'transaction':
                $transactionSearchModel = new TransactionSearch();
                $transactionDataProvider = $transactionSearchModel->search(Yii::$app->request->queryParams);
                $transactionDataProvider->query->andWhere(['transaction_pack_id' => $model->id]);
        
                // compute total Value on the current grid rows
                $totalValue = $transactionDataProvider->query->sum('value');

                return $this->render('view', [
                    'model' => $model,
                    'tab' => $tab,
                    'tabContent' => $this->renderPartial('_tab-transaction', [
                        'model' => $model,
                        'transactionSearchModel' => $transactionSearchModel,
                        'transactionDataProvider' => $transactionDataProvider,
                        'bankAccounts' => BankAccount::getNameIndex(),
                        'totalValue' => $totalValue
                    ])
                ]);
                break;

            case 'attachment':
                return $this->render('view', [
                    'model' => $model,
                    'tab' => $tab,
                    'tabContent' => $this->renderPartial('/common/_tab-attachment', [
                        'model' => $model,
                    ])
                ]);
                break;

            default:
                return $this->redirect(['view', 'id' => $model->id, 'tab' => 'transaction']);
                break;
        }
    }

    /**
     * Creates a new TransactionPack model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($bank_account_id = null)
    {
        $model = new TransactionPack();

        $bankAccount = null;
        if ($bank_account_id != null) {
            $bankAccount = BankAccount::findOne($bank_account_id);
            if ($bankAccount == null) {
                throw new NotFoundHttpException('The requested bank account does not exist.');
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($bankAccount) {
                $model->link('bankAccount', $bankAccount);
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'bankAccount' => $bankAccount,
            'bankAccounts' => BankAccount::getNameIndex()
        ]);
    }

    /**
     * Updates an existing TransactionPack model.
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
        ]);
    }

    /**
     * Deletes an existing TransactionPack model.
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
     * Finds the TransactionPack model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TransactionPack the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModel($id)
    {
        if (($model = TransactionPack::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
