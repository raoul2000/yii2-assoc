<?php
namespace app\modules\gymv\controllers;

use Yii;
use app\models\Address;
use app\models\Contact;
use app\models\Product;
use app\models\AddressSearch;
use app\modules\gymv\models\ProductSelectionForm;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class RegistrationController extends \yii\web\Controller
{
    const SESS_CONTACT = 'registration.contact';
    const SESS_ADDRESS = 'registration.address';
    const SESS_PRODUCTS = 'registration.products';
    const SESS_ORDERS_1 = 'registration.orders_1';
    const SESS_ORDERS_2 = 'registration.orders_2';

    private $_step = ['contact', 'address', 'order', 'transaction'];
    private $_currentStep = 'contact';

    public function init()
    {
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
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

    private function renderWizard($mainContent)
    {
        return $this->render('index', [
            'wizMain'    => $mainContent,
            'wizSummary' => $this->buildWizSummary(),
        ]);

    }
    private function buildWizSummary()
    {
        $data = [];
        $session = Yii::$app->session;

        if ($session->has(self::SESS_CONTACT)) {
            $data['contact'] = $session[self::SESS_CONTACT];
        }
        if ($session->has(self::SESS_ADDRESS)) {
            $data['address'] = $session[self::SESS_ADDRESS];
        }
        
        return $this->renderPartial('_summary', [
            'data' => $data
        ]);
    }

    public function actionContactSearch()
    {
        //Yii::$app->session->remove(self::SESS_CONTACT);
        //Yii::$app->session->remove(self::SESS_ADDRESS);
        //Yii::$app->session->remove(self::SESS_PRODUCTS);

        $contact = new Contact();
        $contact->is_natural_person = true;

        if (Yii::$app->request->getIsPost()) {
            $contactId = Yii::$app->request->post('contactId', null);
            if (empty($contactId)) {
                // contact was not found in DB : create a new one
                $contact->name = '';
            } elseif( is_numeric($contactId) ) {
                // existing contact selected (id provided) : validate it exists
                $contact = Contact::find()
                    ->where([
                        'id'                => $contactId,
                        'is_natural_person' => true
                    ])
                    ->one();
                if ($contact == null) {
                    throw new NotFoundHttpException('Contact not found.');
                }    
            } elseif (preg_match('/new-contact@(.+)/', $contactId, $matches, PREG_OFFSET_CAPTURE, 0)) {
                // user entered a contact name that could not be found : create a new contact
                // with entered name (ex : new-contact@Dupond converted to name = 'Dupond')
                $contact->name = $matches[1][0];
            } else {
                throw new NotFoundHttpException('invalid input');
            }
            Yii::$app->session[self::SESS_CONTACT] = $contact->getAttributes();
            $this->redirect(['contact-edit']);
        }

        return $this->renderWizard(
            $this->renderPartial('_contact-search')
        );
    }

    public function actionContactEdit()
    {
        if (!Yii::$app->session->has(self::SESS_CONTACT)) {
            // session variable is not as expected
            $this->redirect(['contact-search']);
        }
        //Yii::$app->session->remove(self::SESS_ADDRESS);

        $model = Contact::create();
        $model->setAttributes(Yii::$app->session[self::SESS_CONTACT]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // save contact to session
            Yii::$app->session[self::SESS_CONTACT] = $model->getAttributes();
            if ( empty($model->address_id)) {
                return $this->redirect(['address-search']);
            }
        }

        return $this->renderWizard(
            $this->renderPartial('_contact-edit', [
                'model' => $model
            ])
        );
    }
    /**
     * REST endpoint to perform address search on both the gouv.fr addresses service and in the
     * database. Results are merged and returned as a JSON object.
     *
     * @return void
     */
    public function actionAjaxAddressSearch()
    {
        if (!Yii::$app->request->isAjax) {
            throw new NotFoundHttpException('invalid input');
        }
        Yii::$app->response->format = Response::FORMAT_JSON; 

        // read query and validate params
        $address = Yii::$app->request->get('address');  // mandatory
        $city = Yii::$app->request->get('city');        // optional
        if (empty($address)) {
            throw new yii\web\BadRequestHttpException('the parameter "address" is missing');
        }

        // performing REST request to the addresses.gouv.fr service
        $wsResult = [];
        $params = ['q' => $address . (!empty($city) ? ' ' . $city : '')];
        try {
            $client = new \yii\httpclient\Client();
            $response = $client->createRequest()
                ->setMethod('GET')
                ->setUrl('https://api-adresse.data.gouv.fr/search')
                ->setData($params)
                ->send();
    
            $wsResult = array_map(function($item){
                $property = $item['properties'];
                return [
                    'id'       => null,
                    'address'  => $property['name'],
                    'city'     => $property['city'],
                    'zip_code' => $property['postcode'],
                    'country'  => 'FRANCE'
                ];
            }, $response->data['features']);
        } catch (\Exception $ex) {
            // Failed silently
            Yii::error('Failed to request api-addresse.data.gouv.fr/search service');
            Yii::error($ex);
        }

        // searching address in the database
        $dbRows = Address::find()
            ->where(['LIKE', 'line_1', $address])
            ->andFilterWhere(['city' => $city])
            ->limit(5)
            ->asArray()
            ->all();

        $dbResult = array_map(function($row){
            return [
                'id'       => $row['id'],
                'address'  => $row['line_1'],
                'city'     => $row['city'],
                'zip_code' => $row['zip_code'],
                'country'  => $row['country']
            ];
        }, $dbRows);

        
        // building response body
        
        // done : return response
        return $dbResult + $wsResult;
    }

    // TODO: if contact is already in DB, then it may already be linked to an address
    // so we should check for existing address
    public function actionAddressSearch()
    {
        if (!Yii::$app->session->has(self::SESS_CONTACT)) {
            // session variable is not as expected
            $this->redirect(['contact-search']);
        }
        //Yii::$app->session->remove(self::SESS_ADDRESS);
        $model = new Address();

        if (Yii::$app->request->getIsPost()) {
            $addressId = Yii::$app->request->post('address_id', null);
            if ($addressId) {
                $model = Address::findOne($addressId);
                if (!$model) {
                    throw new NotFoundHttpException('Address not found.');
                }
            } elseif ($model->load(Yii::$app->request->post()) ) {
                $model->id = null;
            }  else {
                throw new NotFoundHttpException('invalid input');
            }
            Yii::$app->session[self::SESS_ADDRESS] = $model->getAttributes(); 
            $this->redirect(['address-edit']);
        } 
        
        return $this->renderWizard(
            $this->renderPartial('_address-search', [
                'model' => $model
            ])
        );
    }

    public function actionAddressEdit()
    {
        if (!Yii::$app->session->has(self::SESS_ADDRESS)) {
            // session variable is not as expected
            $this->redirect(['address-search']);
        }

        $model = new Address();
        if (Yii::$app->request->isGet) {
            $model->setAttributes(Yii::$app->session[self::SESS_ADDRESS]);
        } elseif ($model->load(Yii::$app->request->post()) && $model->validate()) {
            Yii::$app->session[self::SESS_ADDRESS] = $model->getAttributes();
            return $this->redirect(['product-select']);
        }

        return $this->renderWizard(
            $this->renderPartial('_address-edit', [
                'model' => $model
            ])
        );
    }

    /**
     * Ask the user to select products.
     * Products are split in First class and Second class, each class being displayed 
     * differently from the other.
     *
     * @return void
     */
    public function actionProductSelect()
    {
        if (!Yii::$app->session->has(self::SESS_ADDRESS)) {
            $this->redirect(['address-search']);
        }

        // This is the configured list of ids for first class products 
        // they are displayed as a checkbox list in the first col
        $firstClassProductIds = [ 1, 2, 3];

        $model = new ProductSelectionForm();
        $model->setCategory1ProductIds($firstClassProductIds);

        if (Yii::$app->request->isGet && Yii::$app->session->has(self::SESS_PRODUCTS)) {
            foreach (Yii::$app->session[self::SESS_PRODUCTS] as $productAttributes) {
                $model->product_ids[] = $productAttributes['id'];
            }
        } else if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ( empty($model->product_ids)) {
                Yii::$app->session->remove(self::SESS_PRODUCTS);
            } else {
                Yii::$app->session[self::SESS_PRODUCTS] = $model->querySelectedProductModels()
                    ->asArray()
                    ->all();
            }
        }

        // prepare to render the view

        $rows = \app\models\Product::find()
            ->where(['in', 'id', $firstClassProductIds])
            ->asArray()
            ->all();        
        // [ productId => productName]
        $firstClassProductIndex = ArrayHelper::map($rows, 'id', 'name');

        $products_2 = $model
            ->querySelectedProductModels(ProductSelectionForm::CATEGORY_2)
            ->indexBy('id')
            ->asArray()
            ->all();

        return $this->renderWizard(
            $this->renderPartial('_product-select', [
                'model' => $model,
                'firstClassProductIndex' => $firstClassProductIndex,
                'products_2' => $products_2
            ])
        );
    }

    public function actionOrder()
    {
        if (!Yii::$app->session->has(self::SESS_PRODUCTS)) {
            $this->redirect(['product-select']);
        }

        $productIdsForm = new ProductSelectionForm();
        foreach (Yii::$app->session[self::SESS_PRODUCTS] as $productAttributes) {
            $model->product_ids[] = $productAttributes['id'];
        }

        // maintain 2 groups : top products and class 2 products (courses)
        $products_1 = \app\models\Product::find()
            ->where(['in', 'id', $productIdsForm->getSelectedProductIdsByCategory(ProductSelectionForm::CATEGORY_1) ])
            ->indexBy('id')
            ->all();

        $products_2 = \app\models\Product::find()
            ->where(['in', 'id', $productIdsForm->getSelectedProductIdsByCategory(ProductSelectionForm::CATEGORY_2) ])
            ->indexBy('id')
            ->all();

        $orders_1 = array_map(function($product) {
            return new \app\models\Order([
                'product_id' => $product->id,
                'value' => $product->value
            ]);
        }, $products_1);        

        $orders_2 = array_map(function($product) {
            return new \app\models\Order([
                'product_id' => $product->id,
                'value' => $product->value
            ]);
        }, $products_2);        

        if( \Yii::$app->request->isPost) {
            
            Model::loadMultiple($orders_1, Yii::$app->request->post());
            Model::loadMultiple($orders_2, Yii::$app->request->post());

            $orders1AreValid = Model::validateMultiple($orders_1);
            $orders2AreValid = Model::validateMultiple($orders_2);

            if ($orders1AreValid && $orders2AreValid) {
                Yii::$app->session[self::SESS_ORDERS_1] = $orders_1->getAttributes();
                Yii::$app->session[self::SESS_ORDERS_2] = $orders_2->getAttributes();
                return $this->redirect(['transaction']);
            }
        }

        return $this->renderWizard(
            $this->renderPartial('_order', [
                'orders_1'   => $orders_1,
                'orders_2'   => $orders_2,
                'products_1' => $products_1,
                'products_2' => $products_2,
            ])
        );
    }

    public function actionTransaction()
    {
        return $this->renderWizard(
            $this->renderPartial('_transaction', [
                'orders_1'   => $orders_1,
                'orders_2'   => $orders_2,
                'products_1' => $products_1,
                'products_2' => $products_2,
            ])
        );
    }
    public function actionIndex()
    {
        return $this->render('index');
    }
}
