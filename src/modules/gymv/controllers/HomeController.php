<?php

namespace app\modules\gymv\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use \app\models\Contact;
use \app\models\ContactSearch;
use \app\models\Product;
use \app\models\Order;
use \app\components\SessionDateRange;

class HomeController extends \yii\web\Controller
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
    
    public function actionIndex()
    {
        // prepare query : search for contact refering to a real person
        $searchModel = new ContactSearch();
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams
        );

        $dataProvider
            ->query
            ->andWhere(['is_natural_person' => true]);

        // a list of product ids identifying registered contacts may have been configured 
        // as a comma separated list of product ids.
        $val = Yii::$app->configManager->getItemValue('product.consumed.by.registered.contact');
        // build an array out of a comma separated list of values (trim and remove empty)
        $productIds = array_filter(
            array_map(function($item) {
                return trim($item);
            }, explode(',', $val)), 
            function($item) {
                return !empty($item);
            }
        );

        if (count($productIds) != 0) {
            // The contactIdsQuery used to get the list of contact ids who are valid consumers
            $contactIdQuery = Order::find()
                ->select('to_contact_id')
                ->distinct()
                ->validInDateRange(SessionDateRange::getStart(), SessionDateRange::getEnd());

            // validate product id list : checks that all product ids configured are matching real products
            $productCount = Product::find()
                ->where(['in', 'id', $productIds])
                ->count();
    
            if ( $productCount != count($productIds)) {
                throw new \yii\web\ServerErrorHttpException('The list of products to select contacts contains an invalid Id : (' . implode(', ', $productIds)
                    . ') - please update the configuration and try again');
            }
            // add condition on product id to the existing query
            $contactIdQuery->andWhere( ['in', 'product_id'     , $productIds]);
    


            $providerId =  Yii::$app->configManager->getItemValue('contact_id');
            // validate contact id (provider): check that the condition on the provider contact id is correct
            if (!empty($providerId) && !Contact::find(['id' => $providerId])->exists()) {
                throw new \yii\web\ServerErrorHttpException('THe configured Contact ID to use as a provider, could not be found : (' . $providerId .')' );                
            } elseif( !empty($productCount)) {
                // ... add condition on provider contact 
                $contactIdQuery->andWhere(['=' , 'from_contact_id', $providerId]);
            }
            
            // query is done, run it : find all ids for contacts who are valid consumers of one product in a product set
            $contactIds = $contactIdQuery
                ->asArray()
                ->all();
    
            if (count($contactIds) != 0) {
                $contactIds = \array_map(function($item) {
                    return $item['to_contact_id'];
                }, $contactIds);
            }
            
            $dataProvider
                ->query
                ->andWhere(['in', 'id', $contactIds]);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);

        // find all phisical person contact  who are beneficiary of a given product that is still valid now and that was provided by my
        return $this->render('index');
    }
}
