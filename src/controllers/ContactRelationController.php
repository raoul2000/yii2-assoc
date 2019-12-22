<?php

namespace app\controllers;

use Yii;
use app\components\Constant;
use app\models\Contact;
use app\models\ContactRelation;
use app\models\ContactRelationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\components\SessionDateRange;

/**
 * ContactRelationController implements the CRUD actions for ContactRelation model.
 */
class ContactRelationController extends Controller
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

    /**
     * Lists all ContactRelation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ContactRelationSearch();
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            ContactRelation::find()
                ->andWhereValidInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd())
                ->with(['sourceContact', 'targetContact'])
        );

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'contacts' => Contact::getNameIndex(),
            'contactRelationTypes' => ArrayHelper::map(Constant::getContactRelationTypes(), 'id', 'name')
        ]);
    }

    /**
     * Displays a single ContactRelation model.
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
     * Creates a new ContactRelation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($source_contact_id = null, $redirect_url = null)
    {
        $model = new ContactRelation();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (!empty($redirect_url)) {
                return $this->redirect($redirect_url);
            } else {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        if (isset($source_contact_id)) {
            $model->source_contact_id = $source_contact_id;
        }
        return $this->render('create', [
            'model' => $model,
            'contacts' => Contact::getNameIndex(),
            'contactRelationTypes' => ArrayHelper::map(Constant::getContactRelationTypes(),'id', 'name')
        ]);
    }

    /**
     * Updates an existing ContactRelation model.
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
            'contactRelationTypes' => ArrayHelper::map(Constant::getContactRelationTypes(),'id', 'name')
        ]);
    }

    /**
     * Deletes an existing ContactRelation model.
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
     * Finds the ContactRelation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ContactRelation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ContactRelation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
