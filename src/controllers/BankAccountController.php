<?php

namespace app\controllers;

use Yii;
use app\models\Contact;
use app\models\BankAccount;
use app\models\BankAccountSearch;
use app\models\TransactionPackSearch;
use app\models\TransactionSearch;
use app\models\TransactionPerAccountSearch;
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
     * This view displays details info about a bank account and a tab widget
     * focusing on TRANSACTIONS and TRANSACTION PACKS.
     *
     * @param integer $id ID of the bank account to display
     * @param string $tab the ID of the tab to display
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $tab = 'transaction')
    {
        $bankAccount =  $this->findModel($id);

        switch ($tab) {
            case 'transaction': // --------------------------------------------
                $transactionSearchModel = new TransactionPerAccountSearch();
                $transactionDataProvider = $transactionSearchModel->search(
                    Yii::$app->request->queryParams,
                    $bankAccount
                );
        
                $transactionDataProvider->query
                    ->with(['fromAccount', 'toAccount']);

                // for filter : remove this bank account
                $bankAccounts = array_filter(BankAccount::getNameIndex(), function ($accountId) use ($bankAccount) {
                    return $accountId != $bankAccount->id;
                }, ARRAY_FILTER_USE_KEY);

                return $this->render('view', [
                    'model' => $bankAccount,
                    'tab' => $tab,
                    'accountBalance' => $bankAccount->getBalanceInfo(),
                    'tabContent' => $this->renderPartial('_tab-transaction', [
                        'model' => $bankAccount,
                        'transactionDataProvider' => $transactionDataProvider,
                        'transactionSearchModel' => $transactionSearchModel,
                        'bankAccounts' => $bankAccounts
                    ])
                ]);
            break;
            case 'pack': // ----------------------------------------------------
                $transactionPackSearchModel = new TransactionPackSearch();
                $transactionPackDataProvider = $transactionPackSearchModel->search(
                    Yii::$app->request->queryParams
                );
                $transactionPackDataProvider
                    ->query
                    ->andWhere(['bank_account_id' => $bankAccount->id ]);
    
                return $this->render('view', [
                    'model' => $bankAccount,
                    'tab' => $tab,
                    'accountBalance' => $bankAccount->getBalanceInfo(),
                    'tabContent' => $this->renderPartial('_tab-transaction-pack', [
                        'model' => $bankAccount,
                        'transactionPackSearchModel' => $transactionPackSearchModel,
                        'transactionPackDataProvider' => $transactionPackDataProvider,
                    ])
                ]);
            break;
            default:
                return $this->redirect(['view', 'id' => $bankAccount->id, 'tab' => 'transaction']);
            break;
        }
    }

    /**
     * Creates a new BankAccount model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($contact_id = null)
    {
        $model = new BankAccount();

        $contact = null;
        if ($contact_id !== null) {
            $contact = Contact::findOne($contact_id);
            if ($contact == null) {
                throw new NotFoundHttpException('The requested contact does not exist.');
            }
            if ($model->contact_id == null) {
                $model->contact_id = $contact->id;
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'contact' => $contact,
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
