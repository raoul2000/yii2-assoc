<?php

namespace app\controllers;

use Yii;

use yii\filters\AccessControl;
use app\models\Attachment;
use app\models\Address;
use app\models\AddressSearch;
use app\models\BankAccount;
use app\models\ContactRelation;
use app\models\Contact;
use app\models\ContactSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\SessionDateRange;
use yii\helpers\Url;

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
            ],
            // tags Actions /////////////////////////////////////////

            'query-tags' => [
                'class' => 'app\components\actions\TagListAction',
            ],
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

        // apply tag search condition if tag values have been submitted
        $tagValues = Yii::$app->request->get('tagValues');
        if (!empty($tagValues)) {
            $dataProvider
                ->query
                ->anyTagValues($tagValues);      
        }

        if (\app\components\widgets\DownloadDataGrid::isDownloadRequest()) {
            // request for downloading report

            // columns that depends on contact being a person or not
            if ($tab == 'person') {
                $typedColumns = [
                    ['attribute' => 'name',         'label' => 'name'],
                    ['attribute' => 'firstname',    'label' => 'firstname'],
                    ['attribute' => 'gender'],
                    ['attribute' => 'birthday']
                ];
            } else {
                $typedColumns = [
                    ['attribute' => 'name',         'label' => 'raison sociale'],
                    ['attribute' => 'firstname',    'label' => 'complément'],
                ];
            }

            $exporter = new \yii2tech\csvgrid\CsvGrid(
                [
                    'dataProvider' => new \yii\data\ActiveDataProvider([
                        'query' => $dataProvider->query,
                        'pagination' => [
                            'pageSize' => 100, // export batch size
                        ],
                    ]),
                    'columns' => array_merge(
                        $typedColumns,
                        [
                            ['attribute' => 'email'],
                            ['attribute' => 'address.line_1'],
                            ['attribute' => 'address.line_2'],
                            ['attribute' => 'address.line_3'],
                            ['attribute' => 'address.city'],    
                        ]
                    )
                ]
            );
            \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
            return $exporter->export()->send('contacts.csv');
        } else {
            // request to render
            return $this->render('index', [
                'searchModel'  => $searchModel,
                'dataProvider' => $dataProvider,
                'tagValues'    => $tagValues,
                'tab'          => $tab
            ]);
        }
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
                $bankAccountDataProvider = $bankAccountSearchModel->search(
                    Yii::$app->request->queryParams
                );
                
                $bankAccountDataProvider
                    ->query
                    ->andWhere(['contact_id' => $id]);
    
                return $this->render('view', [
                    'model' => $model,
                    'tab' => $tab,
                    'tabContent' => $this->renderPartial('_tab-account', [
                        'bankAccountDataProvider' => $bankAccountDataProvider,
                        'model' => $model
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

            case 'relation':
                    
                $relations = ContactRelation::find()
                    ->andWhereValidInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
                    ->andWhere([
                        'or',
                            ['source_contact_id' => $id],
                            ['target_contact_id' => $id]
                    ])
                    ->with(['sourceContact', 'targetContact'])
                    ->all();

                return $this->render('view', [
                    'model' => $model,
                    'tab' => $tab,
                    'tabContent' => $this->renderPartial('_tab-relation', [
                        'model' => $model,
                        'relations' => $relations,
                    ])
                ]);
                break;

            case 'address':
                // search 
                $address = $model->address;
                $searchUrl = null;
                if($address) {
                    $searchUrl = 'https://www.pagesjaunes.fr/pagesblanches/recherche?quoi'
                        .'qui=' . $model->name . '&ou=' . urlencode(
                            $address->line_1 . ' ' . $address->zip_code
                        ) 
                        .'&proximite=0';
                }
                return $this->render('view', [
                    'model' => $model,
                    'tab' => $tab,
                    'tabContent' => $this->renderPartial('_tab-address', [
                        'model' => $model,
                        'searchUrl' => $searchUrl
                    ])
                ]);
                break;

            case 'order':
                $orderSearchModel = new \app\models\OrderSearch();
                $orderDataProvider = $orderSearchModel->search(
                    Yii::$app->request->queryParams
                );

                $orderDataProvider
                    ->query
                    ->andWhereValidInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
                    ->andWhere([
                        'or',
                            ['to_contact_id' => $id],
                            ['from_contact_id' => $id]
                    ])
                    ->with('transactions');
                    
                // compute total Value on the current grid rows
                $totalValue = $orderDataProvider->query->sum('value');                     

                return $this->render('view', [
                    'model' => $model,
                    'tab' => $tab,
                    'tabContent' => $this->renderPartial('_tab-order', [
                        'model' => $model,
                        'orderSearchModel' => $orderSearchModel,
                        'orderDataProvider' => $orderDataProvider,
                        'products' => \app\models\Product::getNameIndex(),
                        'contacts' => \app\models\Contact::getNameIndex(),
                        'totalValue' => $totalValue
                    ])
                ]);
                break;
            
            default:
                return $this->redirect(['view', 'id' => $model->id, 'tab' => 'account']);
                break;
        }
    }

    /**
     * NOT USED
     */
    public function actionOrderSummary($id)
    {
        $model = $this->findModel($id);
        $queryOrders = \app\models\Order::find()
            ->where([
                'or', ['to_contact_id' => $id], ['from_contact_id' => $id] 
            ])
            ->andWhereValidInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
            ->asArray();
        /* could it be replace by ...
        $queryOrders = $model
            ->getOrders()
            ->andWhereValidInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
            ->asArray();
        */


        $byProduct = [];
        // group orders per product
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

        // associative array :
        // key : p + product_id
        // value : DateRangeSet
        $validity = [];
        foreach ($byProduct as $productKey => $orders) {
            if ($orders[0]['from_contact_id'] === $model->id) {
                // contact is the producer
                continue;
            }
            // create initial date range  Set with the first Validity date Range
            $validity[$productKey] = new DateRangeSet(
                new DateRange(
                    $order['valid_date_start'], 
                    $order['valid_date_end']
                )
            );

            if( count($orders) === 1) {
                // contact is a consumer but did not return this product : validity is not modified
                continue;
            }

            // contact consumed the product and has done other exchanges that will extend or reduce
            // the validity date range of the consumed product
            for ($idx=1; $idx < count($orders); $idx++) { 
                $order = $orders[$idx];
                
                $orderValidity = new DateRange($order['valid_date_start'], $order['valid_date_end']);
                if( $order['from_contact_id'] == $id) {
                    // contact is the provider : reduce validity date range
                    $validity[$productKey]->substract($orderValidity);
                } else {
                    // contact is the consumer : expand validity date range
                    $validity[$productKey]->add($orderValidity);
                }
            }
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
            'person' => $person,
            'cancelUrl' => Url::to(['/contact/index'])
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
            'cancelUrl' => Url::to(['/contact/view', 'id' => $id])
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
