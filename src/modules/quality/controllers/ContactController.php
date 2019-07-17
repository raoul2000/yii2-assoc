<?php

namespace app\modules\quality\controllers;

use Yii;
use yii\web\Controller;
use app\models\Contact;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `quality` module
 */
class ContactController extends BaseController
{
    protected $pageSubHeader = 'Contact';
    protected $viewModelRoute = '/contact/view';
    protected $dataColumnNames = ['id', 'name', 'firstname'];

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->implActionIndex($this->getMetrics());
    }

    public function actionViewData($id)
    {
        return $this->implActionViewData($this->getMetrics(), $id);
    }

    private function getMetrics()
    {
        return [
           'email-null' => [
                'query' => Contact::find()->where([
                    'is_natural_person' => true,
                    'email' => null]),
                'label' => 'Personnes dont <b>l\'adresse Email</b> est manquante'
                ],
           'firstname-null' => [
                'query' => Contact::find()->where([
                    'is_natural_person' => true,
                    'firstname' => null]),
                'label' => 'Personnes dont le <b>prénom</b> est manquant'
                ],
           'birthday-null' => [
                'query' => Contact::find()->where([
                    'is_natural_person' => true,
                    'birthday' => null]),
                'label' => 'Personnes dont la <b>date de naissance</b> est manquante'
                ],
            'centenary' => [
                'query' => Contact::find()
                    ->where(['is_natural_person' => true])
                    ->andWhere([ 'is not', 'birthday' , null])
                    ->andWhere([ '>', 'YEAR(CURDATE()) - YEAR(birthday)' , 110]),
                'label' => 'Personnes dont la <b>date de naissance</b> est à vérifier'
                ],
           'gender-null' => [
                'query' => Contact::find()
                    ->where(['is_natural_person' => true])
                    ->andwhere(['in', 'gender', [null, 0]]),
                'label' => 'Personnes dont le <b>genre</b> n\'est pas déterminé'
                ],
           'address-null' => [
                'query' => Contact::find()->where([
                    'is_natural_person' => true,
                    'address_id' => null]),
                'label' => 'Personnes qui ne sont pas réliées à une <b>adresse</b>'
            ]
        ];
    }
}
