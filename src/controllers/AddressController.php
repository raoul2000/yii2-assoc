<?php

namespace app\controllers;

use Yii;
use app\models\Contact;
use app\models\Address;
use app\models\AddressSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AddressController implements the CRUD actions for Address model.
 */
class AddressController extends Controller
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
     * Lists all Address models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AddressSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Address model.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Address model.
     * If creation is successful, the browser will be redirected to the 'view' page or to
     * $redirect_url if it is not null.
     *
     * @param int $contact_id
     * @param string $redirect_url
     * @return mixed
     */
    public function actionCreate($contact_id = null, $redirect_url = null)
    {
        $model = new Address();
        $contact = null;

        if (isset($contact_id)) {
            $contact = Contact::findOne($contact_id);
            if ($contact == null) {
                throw new NotFoundHttpException('Contact not found.');
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (isset($contact)) {
                $contact->updateAttributes([
                    'address_id' => $model->id
                ]);
            }
            if ($redirect_url === null) {
                $redirect_url = ['view', 'id' => $model->id];
            }
            return $this->redirect($redirect_url);
        }

        return $this->render('create', [
            'model' => $model,
            'contact' => $contact,
            'redirect_url' => ($redirect_url ? $redirect_url : ['index'])
        ]);
    }

    /**
     * Updates an existing Address model.
     * If update is successful, the browser will be redirected to the 'view' pageor to
     * $redirect_url if it is not null.
     *
     * @param integer $id
     * @param int $contact_id if not null, the form is displayed as an address related to this
     * contact
     * @param string $redirect_url
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $contact_id = null, $redirect_url = null)
    {
        $model = $this->findModel($id);

        $contact = null;
        if (isset($contact_id)) {
            $contact = Contact::findOne($contact_id);
            if ($contact == null) {
                throw new NotFoundHttpException('Contact not found.');
            }
        }
        // if this address is shared by several contact, list them in $otherContact
        // but ignore the current contact (if defined)
        $otherContacts = array_filter($model->contacts, function ($value) use ($contact_id) {
            return $value->id != $contact_id;
        });

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($redirect_url === null) {
                $redirect_url = ['view', 'id' => $model->id];
            }
            return $this->redirect($redirect_url);
        }

        return $this->render('update', [
            'model' => $model,
            'contact' => $contact,
            'otherContacts' => $otherContacts,
            'redirect_url' => ($redirect_url ? $redirect_url : ['index'])
        ]);
    }

    /**
     * Deletes an existing Address model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
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
     * Finds the Address model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Address the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Address::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
