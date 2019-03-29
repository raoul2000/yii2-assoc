<?php

namespace app\controllers;

use Yii;
use app\models\Contact;
use app\models\BankAccount;
use app\models\BankAccountSearch;
use app\models\TransactionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BankAccountController implements the CRUD actions for BankAccount model.
 */
class BankAccountController extends Controller
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
     * Lists all BankAccount models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BankAccountSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'contacts' => Contact::getNameIndex()
        ]);
    }

    /**
     * Displays a single BankAccount model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $bankAccount =  $this->findModel($id);
        $transactionSearchModel = new TransactionSearch();
        $transactionDataProvider = $transactionSearchModel->search(
            Yii::$app->request->queryParams,
            $bankAccount->getTransactions()
        );

        return $this->render('view', [
            'model' => $bankAccount,
            'accountBalance' => $bankAccount->getBalance(),
            'transactionSearchModel' => $transactionSearchModel,
            'transactionDataProvider' => $transactionDataProvider,
            'bankAccounts' => BankAccount::getNameIndex()
        ]);
    }

    /**
     * Creates a new BankAccount model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BankAccount();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'contacts' => Contact::getNameIndex()
        ]);
    }

    /**
     * Updates an existing BankAccount model.
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
            'contacts' => Contact::getNameIndex()
        ]);
    }

    /**
     * Deletes an existing BankAccount model.
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
     * Finds the BankAccount model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BankAccount the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BankAccount::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
