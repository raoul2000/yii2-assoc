<?php

namespace app\modules\quality\controllers;

use Yii;
use yii\web\Controller;
use app\models\Address;
use app\models\Contact;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `quality` module
 */
class AddressController extends BaseController
{
    protected $pageSubHeader = 'Address';
    protected $viewModelRoute = '/address/view';
    protected $dataColumnNames = ['id', 'line_1', 'city', 'country'];

    private $colNameOptions = [
        'city' => 'City',
        //'line_1' => 'Address line 1'
    ];
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex($tab = '', $colName = '')
    {
        $this->view->params['breadcrumbs'][] = ['label' => 'Addresses', 'url' => ['/address/index']];
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
                    Address::find()
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
     * Returns metrics for Address
     *
     * @return array
     */
    private function getMetrics()
    {
        // address with no contact
        $queryResult = Contact::find()
            ->select('address_id')
            ->distinct()
            ->where([ 'not', ['address_id' => null]])
            ->asArray()
            ->all();

        $assignedAddressIds = array_map(function ($item) {
            return $item['address_id'];
        }, $queryResult);

        $queryNotAssignedToContact = Address::find()
            ->where(['not in', 'id', $assignedAddressIds ]);
        
        // returns metrics data 
        return [
           'no-line' => [
                'query' => Address::find()->where([
                    'line_1' => '',
                    'line_2' => '',
                    'line_3' => '']),
                'label' => 'Adresses sans <b>intitulé</b>'
                ],
           'no-city' => [
                'query' => Address::find()->where([
                    'city' => '']),
                'label' => 'Adresses sans <b>ville</b>'
                ],
           'no-country' => [
                'query' => Address::find()->where([
                    'country' => '']),
                'label' => 'Adresses sans <b>pays</b>'
                ],
           'no-zip-code' => [
                'query' => Address::find()->where([
                    'zip_code' => '']),
                'label' => 'Adresses sans <b>Code Postal</b>'
                ],
           'not-assigned' => [
                'query' => $queryNotAssignedToContact,
                'label' => 'Adresses qui ne sont <b>pas assignées à un Contact</b>'
                ],
        ];
    }
}
