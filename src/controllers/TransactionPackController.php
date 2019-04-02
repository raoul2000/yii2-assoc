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
        ];
    }

    /**
     * Lists all TransactionPack models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TransactionPackSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
        $transactionDataProvider = $transactionSearchModel->search(Yii::$app->request->queryParams);
        $transactionDataProvider->query->andWhere(['transaction_pack_id' => null]);
        
        return $this->render('link-transaction', [
            'transactionPack' => $transactionPack,
            'transactionSearchModel' => $transactionSearchModel,
            'transactionDataProvider' => $transactionDataProvider,
            'bankAccounts' => BankAccount::getNameIndex()
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
     * Displays a single TransactionPack model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model =  $this->findModel($id);
        $transactionSearchModel = new TransactionSearch();
        $transactionDataProvider = $transactionSearchModel->search(Yii::$app->request->queryParams);
        $transactionDataProvider->query->andWhere(['transaction_pack_id' => $model->id]);

        return $this->render('view', [
            'model' => $model,
            'transactionSearchModel' => $transactionSearchModel,
            'transactionDataProvider' => $transactionDataProvider,
            'bankAccounts' => BankAccount::getNameIndex()
        ]);
    }

    /**
     * Creates a new TransactionPack model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TransactionPack();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
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
    protected function findModel($id)
    {
        if (($model = TransactionPack::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
