<?php

namespace app\controllers;

use Yii;

use yii\filters\AccessControl;
use app\models\Attachment;
use app\models\Address;
use app\models\AddressSearch;
use app\models\BankAccount;
use app\models\Contact;
use app\models\ContactSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\SessionDateRange;

/**
 * ContactController implements the CRUD actions for Contact model.
 */
class ContactController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        //'permissions' => ['contact-manager'],
                    ]
                ],
            ],
        ];
    }
    public function actions()
    {
        return [
            // declares "error" action using a class name
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
            'stat-data' => [
                'class' => 'app\components\actions\contacts\stat\AjaxStatAction'
            ]
        ];
    }
    /**
     * Lists all Contact models.
     * Displays a Tab view with "person" and "organization" Tabs
     *
     * @param string $tab the active tab name
     * @return mixed
     */
    public function actionIndex($tab = 'person')
    {
        $searchModel = new ContactSearch();
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams
        );
        $dataProvider->query->andWhere(['is_natural_person' => ($tab == 'person')]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'tab' => $tab
        ]);
    }

    /**
     * Export all contacts as a CSV file
     *
     * @return void
     */
    public function actionExportCsv()
    {
        $exporter = new \yii2tech\csvgrid\CsvGrid(
            [
            'dataProvider' => new \yii\data\ActiveDataProvider([
                'query' => Contact::find(),
                'pagination' => [
                    'pageSize' => 100, // export batch size
                ],
            ]),
            'columns' => [
                [
                    'attribute' => 'name',
                ],
                [
                    'attribute' => 'created_at',
                    'format' => 'date',
                ],
            ],
            ]
        );
        \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        return $exporter->export()->send('contacts.csv');
    }
    /**
     * Displays a single Contact model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $tab = 'address')
    {
        // load main contact model
        $model = $this->findModel($id);
        switch ($tab) {
            case 'account':
                $bankAccountSearchModel = new \app\models\BankAccountSearch();
                $bankAccountDataProvider = $bankAccountSearchModel->search(Yii::$app->request->queryParams);
                $bankAccountDataProvider->query->andWhere(['contact_id' => $id]);
    
                return $this->render('view', [
                    'model' => $model,
                    'tab' => $tab,
                    'tabContent' => $this->renderPartial('_tab-account', [
                        'bankAccountDataProvider' => $bankAccountDataProvider,
                        'model' => $model,
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

            case 'address':
                return $this->render('view', [
                    'model' => $model,
                    'tab' => $tab,
                    'tabContent' => $this->renderPartial('_tab-address', [
                        'model' => $model,
                    ])
                ]);
                break;
            
            default:
                return $this->redirect(['view', 'id' => $model->id, 'tab' => 'account']);
                break;
        }
    }

    /**
     * Display orders for a contact, given the contact id.
     *
     * @param int $id the contact model id
     * @return mixed
     */
    public function actionOrder($id)
    {
        $model = $this->findModel($id);
        $orderSearchModel = new \app\models\OrderSearch();
        $orderDataProvider = $orderSearchModel->search(
            Yii::$app->request->queryParams,
            \app\models\Order::find()
                ->with('transactions')
        );
        //$orderDataProvider->query->andWhere(['to_contact_id' => $id]);
        $orderDataProvider->query->andWhere([
            'or', ['to_contact_id' => $id], ['from_contact_id' => $id] 
        ]);

        return $this->render('order', [
            'model' => $model,
            'orderSearchModel' => $orderSearchModel,
            'orderDataProvider' => $orderDataProvider,
            'products' => \app\models\Product::getNameIndex(),
            'contacts' => \app\models\Contact::getNameIndex()
        ]);
    }
    /**
     * 
     */
    public function actionOrderSummary($id)
    {
        $model = $this->findModel($id);
        $queryOrders = \app\models\Order::find()
            ->where([
                'or', ['to_contact_id' => $id], ['from_contact_id' => $id] 
            ])
            ->validInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
            ->asArray();

        $byProduct = [];
        foreach ($queryOrders->each() as $order) {
            // data is being fetched from the server in batches of 100,
            // but $order represents one row of data from the order table
            // @see https://www.yiiframework.com/doc/guide/2.0/en/db-query-builder#batch-query

            $productKey = "p" . $order['product_id'];
            if (!array_key_exists($productKey, $byProduct)) {
                $byProduct[$productKey] = [];
            }
            $byProduct[$productKey][] = $order;
        }        

        return $this->render('order-summary', [
            'model' => $model,
            'byProduct' => $byProduct,
            'products' => \app\models\Product::getNameIndex(),
            'contacts' => \app\models\Contact::getNameIndex()
        ]);
    }
    /**
     * Creates a new Contact model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($person = null)
    {
        $model = Contact::create();
        
        if (isset($person) && !isset($model->is_natural_person)) {
            $model->is_natural_person = ($person == true);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // by default, create an account for new contact
            $bankAccount = new BankAccount();
            $bankAccount->contact_id = $model->id;
            $bankAccount->name = '';
            $bankAccount->save(false);

            return $this->redirect(['view', 'id' => $model->id]);
        }


        return $this->render('create', [
            'model' => $model,
            'person' => $person
        ]);
    }
    /**
     * Updates an existing Contact model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
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
     * Link a contact to an address
     * If link is successful, the browser will be redirected to the 'contact/view' page.
     *
     * @param string $id the contact id
     * @param string $address_id the address id
     * @return mixed
     * @throws NotFoundHttpException if the contact model or the address model cannot be found
     */
    public function actionLinkAddress($id, $address_id = null)
    {
        $model = $this->findModel($id);
        if (isset($address_id)) {
            // address has been selected by the user : validate it
            $address = Address::findOne($address_id);
            if (!isset($address)) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
            // link contact with address
            $model->updateAttributes([
                'address_id' => $address->id
            ]);
            return $this->redirect(['view', 'id' => $model->id, 'tab' => 'address']);
        }

        // Search for all existing addresses, including the onens already linked (as
        // an address can be linked to several contacts)
        $addressSearchModel = new AddressSearch();
        $addressDataProvider = $addressSearchModel->search(Yii::$app->request->queryParams);

        return $this->render('link-address', [
            'model' => $model,
            'addressSearchModel' => $addressSearchModel,
            'addressDataProvider' => $addressDataProvider,
        ]);
    }
    /**
     * Unlink a contact from its current linked address.
     * If the contact is not link to any address, this method has no effect.
     * When done, the browser is redirected to the 'view' page of the contact.
     *
     * @param string $id the contact Id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUnlinkAddress($id)
    {
        $model = $this->findModel($id);
        $model->updateAttributes([
            'address_id' => null
        ]);
        return $this->redirect(['view', 'id' => $model->id, 'tab' => 'address']);
    }
    /**
     * Deletes an existing Contact model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        //$this->findModel($id)->softDelete();
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    /**
     * Finds the Contact model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Contact the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModel($id)
    {
        if (($model = Contact::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
