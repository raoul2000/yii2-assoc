<?php
namespace app\modules\gymv\controllers;

use Yii;
use app\models\Address;
use app\models\Contact;
use app\models\AddressSearch;
use app\modules\gymv\models\ProductForm;
use yii\web\Response;
use yii\web\NotFoundHttpException;

class RegistrationController extends \yii\web\Controller
{
    const SESS_CONTACT = 'registration.contact';
    const SESS_ADDRESS = 'registration.address';
    const SESS_PRODUCTS = 'registration.products';

    private $_step = ['contact', 'address', 'order', 'transaction'];
    private $_currentStep = 'contact';

    public function init()
    {
        parent::init();
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
        
        return $this->renderPartial('_summary', [
            'value' => 'value'
        ]);
    }

    public function actionContactSearch()
    {
        //Yii::$app->session->remove(self::SESS_CONTACT);
        //Yii::$app->session->remove(self::SESS_ADDRESS);

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
    //TODO: searcg in both addresses.gouv api and internal DB and then merge results
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
        if (true) {
            $params = ['q' => $address . (!empty($city) ? ' ' . $city : '')];
            
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
        }

        // searching in DB
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

    public function actionProductSelect()
    {
        $model = new ProductForm();
        if (!Yii::$app->session->has(self::SESS_ADDRESS)) {
            $this->redirect(['address-search']);
        }
        //Yii::$app->session->remove(self::SESS_PRODUCTS);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            Yii::$app->session[self::SESS_PRODUCTS] = $model->getAttributes();
            return $this->redirect(['order']);
        }
        $products_2 = [];
        if (Yii::$app->session->has(self::SESS_PRODUCTS)) {
            $model->setAttributes(Yii::$app->session[self::SESS_PRODUCTS]);

            $products_2 = \app\models\Product::find()
                ->where(['in', 'id', $model->products_2])
                ->asArray()
                ->indexBy('id')
                ->all();
        }


        return $this->renderWizard(
            $this->renderPartial('_product-select', [
                'model' => $model,
                'products_2' => $products_2
            ])
        );
    }

    public function actionOrder()
    {
        if (!Yii::$app->session->has(self::SESS_PRODUCTS)) {
            $this->redirect(['product-select']);
        }

        $products = new ProductForm();
        $products->setAttributes(Yii::$app->session[self::SESS_PRODUCTS]);

        return $this->renderWizard(
            $this->renderPartial('_order', [
                'products' => $products
            ])
        );
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
}
