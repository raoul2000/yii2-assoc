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

    public function actionSimilarity($threshold = 70)
    {
        $similarity = [];
        // store index for each compared string pair
        $index = [];

        // query rows 
        $queryResult = Address::find()
            ->select('city')
            ->distinct()
            ->asArray()
            ->all();

        // extract list of values
        $values = array_map(function ($item) {
            return $item['city'];
        }, $queryResult);


        foreach ($values as $idx0 => $value0) {
            foreach ($values as $idx1 => $value1) {
                if ($idx1 == $idx0) {
                    continue; // don't compare with itself
                }

                // because similarity operation is symetric (a.b == b.a) we don't want to perform duplicate similarity computation
                // for this purpose, define the first and second argument using min/max operation, create and store an index out of it

                $first  = min($value0, $value1);
                $second = max($value0, $value1);

                $indexKey = $first . $second;
                if (array_key_exists($indexKey, $index)) {
                    // string pair already processed : skip
                    continue;
                }

                $matchValue = similar_text($first, $second, $percent);
                $index[$indexKey] = $matchValue;    // remember this string pair

                if ($percent > $threshold) {
                    $similarity[] = [
                        'first' => $first,
                        'second' => $second,
                        'match' => $percent
                    ];
                }
            }
        }
        // sort by best match
        usort($similarity, function ($a, $b) {
            if ($a['match'] == $b['match']) {
                return 0;
            }
            return ($a['match'] < $b['match']) ? 1 : -1;
        });

        return $this->render('similarity', [
            'datasetItems' => $similarity
        ]);
    }
}
