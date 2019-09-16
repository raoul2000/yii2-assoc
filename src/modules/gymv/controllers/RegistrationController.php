<?php
namespace app\modules\gymv\controllers;

use Yii;
use app\models\Address;
use app\models\Contact;
use app\models\Product;
use app\models\Order;
use app\models\Transaction;
use app\models\AddressSearch;
use app\modules\gymv\models\ProductSelectionForm;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use \app\components\Helpers\DateHelper;
use \app\components\SessionDateRange;
use \app\components\SessionContact;

class RegistrationController extends \yii\web\Controller
{
    const SESS_CONTACT = 'registration.contact';
    const SESS_ADDRESS = 'registration.address';
    const SESS_PRODUCTS = 'registration.products';
    const SESS_ORDERS = 'registration.orders';
    const SESS_TRANSACTIONS = 'registration.transactions';

    private $_step = ['contact', 'address', 'order', 'transaction'];
    private $_currentStep = 'contact';

    // This is the configured list of ids for first class products 
    // they are displayed as a checkbox list in the first col
    private $_firstClassProductIds = [ 1, 2, 3];

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
        $model->setAttributes(Yii::$app->session[self::SESS_CONTACT], false);

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
            $model->setAttributes(Yii::$app->session[self::SESS_ADDRESS], false);
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

        $model = new ProductSelectionForm();
        $model->setCategory1ProductIds($this->_firstClassProductIds);

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
                return $this->redirect(['order']);
            }
        }

        // prepare to render the view
        $rows = \app\models\Product::find()
            ->where(['in', 'id', $this->_firstClassProductIds])
            ->asArray()
            ->all();        
        $firstClassProductIndex = ArrayHelper::map($rows, 'id', 'name'); // [ productId => productName]

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

        $productModels = new ProductSelectionForm();
        $productModels->setCategory1ProductIds($this->_firstClassProductIds);
        $orderModels = [];
        $products = []; // for rendering only

        // load orders from products selected in the previous step
        $fromContactID = \app\components\SessionContact::getContactId();

        foreach (Yii::$app->session[self::SESS_PRODUCTS] as $product) {
            $order = new Order();
            // compute date range values
            $dateStart = SessionDateRange::getStart();
            if (!empty($product['valid_date_start'])) {
                $dateStart = $product['valid_date_start'];
            }
            $dateEnd = SessionDateRange::getEnd();
            if (!empty($product['valid_date_end'])) {
                $dateEnd = $product['valid_date_end'];
            }

            $order->setAttributes([
                'product_id'       => $product['id'],
                // by CONVENTION : because the contact may be new and so, doesn't have any id,
                // we temporary set the to_contact_id with the same value as the from_contact_id set the
                // This WILL NEED TO BE UPDATED when registration is submited
                'to_contact_id'    => $fromContactID,
                'from_contact_id'  => $fromContactID,
                'value'            => $product['value'],
                'valid_date_start' => DateHelper::toDateAppFormat($dateStart),
                'valid_date_end'   => DateHelper::toDateAppFormat($dateEnd),
            ], false);
            $orderModels[] = $order;

            // we need product name for rendering
            $products[$product['id']] = $product;
        }

        // load validate and save user updates
        if (\Yii::$app->request->isPost) {
            Model::loadMultiple($orderModels, Yii::$app->request->post());
            if (Model::validateMultiple($orderModels)) {
                $ordersToSave = array_map(function($order) {
                    return $order->getAttributes();
                }, $orderModels);

                Yii::$app->session[self::SESS_ORDERS] = $ordersToSave;
                $this->redirect(['transaction']);
            }
        }

        // compute total order value
        $orderTotalValue = 0;
        foreach ($orderModels as $order) {
            $orderTotalValue += $order->value;
        }

        // render Wizard step
        return $this->renderWizard(
            $this->renderPartial('_order', [
                'orderModels' => $orderModels,
                'products' => $products,
                'orderTotalValue' => $orderTotalValue
            ])
        );
    }

    public function actionTransaction()
    {
        // orders MUST be present in the session
        if (!Yii::$app->session->has(self::SESS_ORDERS)) {
            $this->redirect(['order']);
        }

        // compute order total value (for later use)
        $orders = Yii::$app->session[self::SESS_ORDERS];
        $orderTotalValue = 0;
        foreach ($orders as $order) {
            $orderTotalValue += $order['value'];
        }

        // select the from_account_id attribute
        // If the contact exist and has more than one bank_account we must ask which one to use
        $contact = new Contact();
        $contact->setAttributes(Yii::$app->session[self::SESS_CONTACT] , false);
        $fromAccounts = $contact->bankAccounts;

        // holds the list of transactions
        $transactionModels = [];

        if (Yii::$app->request->isGet) {
            // displaying the form : initialize the transaction list with one transaction
            // to cover all order(s) value
            $transaction = new Transaction([
                'value'          => $orderTotalValue,
                'reference_date' => date('d/m/Y')
            ]);
            $transactionModels[] = $transaction;
        } else {
            // POST request : user submit the form fot save or ad/remove transactions

            // populate transactions model list : 
            // 1. create all as empty models 
            $trForms = Yii::$app->request->post('Transaction');
            for ($i=0; $i < count($trForms); $i++) { 
                $transactionModels[] = new Transaction();
            }
            // 2. use "loadMultiple" to assign value to model attributes
            Model::loadMultiple($transactionModels, Yii::$app->request->post());
    
            // 3. force account attributes
            // from_account_id is temprary set to a value (here same as to_account_id) but
            // it will be set to its actual value on save
            foreach ($transactionModels as $transaction) {
                $transaction->from_account_id = SessionContact::getBankAccountId();;
                $transaction->to_account_id = SessionContact::getBankAccountId();
            }

            // handle submit actions : 'action' and optionally 'index'
            $action = Yii::$app->request->post('action');
            switch ($action) {
                case 'add-transaction': ////////////////////////////////////////////////////////
                    $transaction = new Transaction();
                    $transaction->from_account_id = 2;
                    $transaction->to_account_id = 3;        
                    $transaction->value = $orderTotalValue;
                    $transactionModels[] = $transaction;
                    break;

                case 'remove-transaction': ////////////////////////////////////////////////////////
                    $indexToRemove = Yii::$app->request->post('index', null);
                    if ($indexToRemove === null) {
                        throw new NotFoundHttpException('invalid request : missing index');
                    }
                    // remove the transaction based on the index argument
                    $updatedTransactions = [];
                    foreach ($transactionModels as $idx => $value) {
                        if ($idx == $indexToRemove) {
                            continue;
                        }
                        $updatedTransactions[] = $value;
                    }
                    $transactionModels = $updatedTransactions;
                    break;    

                default: ////////////////////////////////////////////////////////
                    // default submit action = standard submit
                    $fromAccountId = Yii::$app->request->post('fromAccountId',null);
                    
                    if (count($fromAccounts) > 1 && empty($fromAccountId)) {
                        // user must select a bank account if contact owns more than one
                        $transactionModels[0]->addError('from_account_id', \Yii::t('app', 'no account selected'));
                    } 
                    elseif (Model::validateMultiple($transactionModels)) {

                        // additional validation
                        $isValid = true;
                        $totalTransaction = 0;
                        foreach ($transactionModels as $transaction) {
                            $totalTransaction += $transaction->value;
                        }
                        // float comparaison
                        // @see https://php.net/manual/en/language.types.float.php
                        if (abs($orderTotalValue-$totalTransaction)>0.01) {
                            Yii::$app->session->setFlash('error', "Order Sum ($orderTotalValue) and Transaction Sum ($totalTransaction) don't match");
                            $isValid = false;
                        }

                        if ($isValid) {
                            $toSave = [];
                            foreach ($transactionModels as $transaction) {
                                //$transaction->from_account_id = $fromAccountId;
                                $toSave[] = $transaction->getAttributes();
                            }
                            \Yii::$app->session[self::SESS_TRANSACTIONS] = $toSave;
                            $this->redirect(['commit']);
                        }
                    }
                    break;
            }
        }

        return $this->renderWizard(
            $this->renderPartial('_transaction', [
                'transactionModels' => $transactionModels,
                'orderTotalValue' => $orderTotalValue,
                'fromAccounts' =>  ArrayHelper::map($fromAccounts, 'id', 'longName'),
                'contact' => $contact
            ])
        );
    }

    public function actionCommit()
    {
        $this->redirect(['transaction']);
        return;

        if (!Yii::$app->session->has(self::SESS_TRANSACTIONS)) {
            $this->redirect(['transaction']);
        }        

        // let's load all models from session
        // contact
        $contact = new Contact();
        $contact->setAttributes(Yii::$app->session[self::SESS_CONTACT], false);

        // address
        $address = new Address();
        $address->setAttributes(Yii::$app->session[self::SESS_ADDRESS], false);

        // orders
        $orders = array_map(function($orderAttr){
            $order = new Order();
            $order->setAttributes($orderAttr, false);
            return $order;
        },Yii::$app->session[self::SESS_ORDERS]);

        // transactions
        $transactions = array_map(function($transactionAttr){
            $transaction = new Transaction();
            $transaction->setAttributes($transactionAttr, false);
            return $transaction;
        },Yii::$app->session[self::SESS_TRANSACTIONS]);
        return ;
        // save/update 

        // address
        // begin with address because contact has a col that points to the address record
        $isNewAddress = $address->isNewRecord;
        $address->save();

        // contact
        $isNewContact = $contact->isNewRecord;
        $contact->address_id = $address->id;
        $contact->save();

        if($isNewContact) {
            // TODO: create bank account
            $bankAccount = new \app\models\BankAccount();
            $bankAccount->contact_id = $contact->id;
            $bankAccount->name = '';
            $bankAccount->save(false);
            
        }

        // transactions
        foreach ($transaction as  $transaction) {
            // set account id
        }



        return $this->render('commit');
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
}
