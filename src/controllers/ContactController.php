<?php

namespace app\controllers;

use Yii;

use yii\filters\AccessControl;
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
    /*
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'permissions' => ['contact-manager'],
                    ]
                ],
            ],
        ];
    }
    */

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
     * Displays a single Contact model.
     * @param string $id
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
     * Creates a new Contact model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Contact();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // by default, create an account for new contact
            $bankAccount = new BankAccount();
            $bankAccount->contact_id = $model->id;
            $bankAccount->name = '';
            $bankAccount->save(false);

            // handle attachment
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
     * If update is successful, the browser will be redirected to the 'contact/view' page.
     * @param string $id
     * @param string $address_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionLinkAddress($id, $address_id = null) 
    {
        $model = $this->findModel($id);
        if( isset($address_id)) {
            $address = Address::findOne($address_id);
            if( !isset($address)) {
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
    protected function findModel($id)
    {
        if (($model = Contact::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
