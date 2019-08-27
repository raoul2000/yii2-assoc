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
use app\components\SessionDateRange;
use yii\helpers\Url;

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
        
                $transactionDataProvider
                    ->query
                    ->dateInRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
                    ->with(['fromAccount', 'toAccount']);

                // for filter : remove this bank account
                $bankAccounts = array_filter(BankAccount::getNameIndex(), function ($accountId) use ($bankAccount) {
                    return $accountId != $bankAccount->id;
                }, ARRAY_FILTER_USE_KEY);

                if (\app\components\widgets\DownloadDataGrid::isDownloadRequest()) {
                    // request for downloading data grid
                    $exporter = new \yii2tech\csvgrid\CsvGrid(
                        [
                            'dataProvider' => new \yii\data\ActiveDataProvider([
                                'query'      => $transactionDataProvider->query,
                                'pagination' => [
                                    'pageSize' => 100, // export batch size
                                ],
                            ]),
                            'columns' => [
                                [
                                    'attribute' => 'id',
                                    'label'     => 'N°',
                                ],
                                ['attribute' => 'reference_date'],
                                'description',
                                'code',
                                [
                                    'attribute' => 'type',
                                    'value'     => function ($model, $key, $index, $column) {
                                        return \app\components\Constant::getTransactionType($model->type);
                                    }
                                ],
                                [
                                    'label'     => 'Account',
                                    'value'     => function ($transactionModel, $key, $index, $column) use ($bankAccount) {
                                        if ($bankAccount->id == $transactionModel->from_account_id) {
                                            return $transactionModel->toAccount->contact_name;
                                        } else {
                                            return $transactionModel->fromAccount->contact_name;
                                        }
                                    }
                                ],
                                [
                                    'label'    => 'credit',
                                    'value'     => function ($transactionModel, $key, $index, $column) use ($bankAccount) {
                                        return $transactionModel->from_account_id == $bankAccount->id
                                        ? ''
                                        : $transactionModel->value;
                                    }
                                ],
                                [
                                    'label'     => 'debit',
                                    'value'     => function ($transactionModel, $key, $index, $column) use ($bankAccount) {
                                        return $transactionModel->from_account_id == $bankAccount->id
                                            ? $transactionModel->value
                                            : '';
                                    }
                                ],
                            ]
                        ]
                    );
                    \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
                    return $exporter->export()->send('account-transactions.csv');
                } else {
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
                }
            break;
            case 'pack': // ----------------------------------------------------
                $transactionPackSearchModel = new TransactionPackSearch();
                $transactionPackDataProvider = $transactionPackSearchModel->search(
                    Yii::$app->request->queryParams
                );
                $transactionPackDataProvider
                    ->query
                    ->dateInRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
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
        $model->initial_value = 0;

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
            'contacts' => Contact::getNameIndex(),
            'cancelUrl' => Url::to(['/bank-account/index'])
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
            'contacts' => Contact::getNameIndex(),
            'cancelUrl' => Url::to(['/bank-account/view', 'id' => $id])
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
