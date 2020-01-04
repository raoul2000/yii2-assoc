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

    private $colNameOptions = [
        'name' => 'Name'
    ];

    public function init()
    {
        parent::init();
        $this->supportedViews = [self::VIEW_ANALYSIS];
    }
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex($tab = '', $colName = '')
    {
        $this->view->params['breadcrumbs'][] = ['label' => 'Contacts', 'url' => ['/contact/index']];
        $this->view->params['breadcrumbs'][] = 'Quality Check';
        $qaView = null;
        switch ($tab) {
            case self::VIEW_ANALYSIS :
                $qaView = $this->renderPartialAnalysisView($this->getMetrics());
                break;
            case self::VIEW_SIMILARITY :
                $qaView = $this->renderPartialSimilarityView(
                    80, 
                    $this->colNameOptions, 
                    $colName, 
                    Contact::find()
                );                
                break;
        }
        return $this->renderExplorer($tab, $qaView);
    }

    public function actionViewData($id)
    {
        return $this->implActionViewData($this->getMetrics(), $id);
    }
    
    /**
     * Returns metrics for Contact
     *
     * @return array
     */
    private function getMetrics()
    {
        return [
           'email-null' => [
                'query' => Contact::find()->where([
                    'is_natural_person' => true,
                    'email' => '']),
                'label' => 'Personnes dont <b>l\'adresse Email</b> est manquante'
                ],
           'firstname-null' => [
                'query' => Contact::find()->where([
                    'is_natural_person' => true,
                    'firstname' => '']),
                'label' => 'Personnes dont le <b>prénom</b> est manquant'
                ],
           'birthday-null' => [
                'query' => Contact::find()
                    ->where(['is_natural_person' => true])
                    ->andWhere([ 'is', 'birthday' , null]),
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
