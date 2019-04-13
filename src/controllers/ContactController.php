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
        ];
    }
    /**
     * Lists all Contact models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ContactSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Export all contacts as a CSV file
     *
     * @return void
     */
    public function actionExportCsv() 
    {
        $exporter = new \yii2tech\csvgrid\CsvGrid([
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
    public function actionView($id)
    {
        $bankAccountSearchModel = new \app\models\BankAccountSearch();
        $bankAccountDataProvider = $bankAccountSearchModel->search(Yii::$app->request->queryParams);
        $bankAccountDataProvider->query->andWhere(['contact_id' => $id]);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'addressCount' => Address::find()->count(),
            'bankAccountDataProvider' => $bankAccountDataProvider
        ]);
    }

    /**
     * Creates a new Contact model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = Contact::create();

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
     * Link a contact with an address
     * If link is successful, the browser will be redirected to the 'contact/view' page.
     * @param string $id the cpntact id
     * @param string $address_id the address id
     * @return mixed
     * @throws NotFoundHttpException if the contact model or the address model cannot be found
     */
    public function actionLinkAddress($id, $address_id = null)
    {
        $model = $this->findModel($id);
        if (isset($address_id)) {
            $address = Address::findOne($address_id);
            if (!isset($address)) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
            $model->updateAttributes([
                'address_id' => $address->id
            ]);
            return $this->redirect(['view', 'id' => $model->id]);
        }

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
        return $this->redirect(['view', 'id' => $model->id]);
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
