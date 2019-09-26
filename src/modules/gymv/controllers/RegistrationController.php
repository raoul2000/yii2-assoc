<?php
namespace app\modules\gymv\controllers;

use Yii;
use app\models\Address;
use app\models\Contact;
use app\models\Product;
use app\models\Order;
use app\models\Transaction;
use app\models\AddressSearch;
use app\models\Category;
use app\modules\gymv\models\ProductSelectionForm;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use \app\components\Helpers\DateHelper;
use \app\components\SessionDateRange;
use \app\components\SessionContact;

class RegistrationController extends \yii\web\Controller
{
    const SESS_CONTACT      = 'registration.contact';
    const SESS_ADDRESS      = 'registration.address';
    const SESS_PRODUCTS     = 'registration.products';
    const SESS_ORDERS       = 'registration.orders';
    const SESS_TRANSACTIONS = 'registration.transactions';

    const DEFAULT_TRANSACTION_TYPE = 'CHQ';

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

    /**
     * Create a default Transaction model suitable for registration
     * Default attributes can be overloaded with the ones passed as argument
     * 
     * @param [type] $attr optional attributes to overload default ones
     * @return Transaction
     */
    private function createDefaultTransaction($attr = null) 
    {
        $transaction = new Transaction([
            'from_account_id'   =>  SessionContact::getBankAccountId(),
            'to_account_id'     =>  SessionContact::getBankAccountId(),    
            'value'             =>  0,
            'type'              =>  self::DEFAULT_TRANSACTION_TYPE,
            'code'              =>  '',
            'category_id'       =>  ( !empty(Yii::$app->params['registration.transaction.categoryId'])
                ? Yii::$app->params['registration.transaction.categoryId']
                : null),
            'reference_date'    => date('d/m/Y') // only the first transaction has reference date initialized                    
        ]);        
        if (!empty($attr)) {
            $transaction->setAttributes($attr, false);
        }
        return $transaction;
    }

    /**
     * Create and returns a list of Products Ids according to configuration
     * 
     * This function is used in the product-select step, to group products and display
     * each group in a differen way (see params.php)
     *
     * @param [type] $conf
     * @return void
     */
    private function buildModelIdList($conf)
    {
        $result = [];
        $query = Product::find()
            ->select('id')
            ->asArray();

        if (array_key_exists('modelId', $conf) && is_array($conf['modelId'])) {
            $query->andWhere([ 'in', 'id', $conf['modelId']]);
        }
        if (array_key_exists('categoryId', $conf) && is_array($conf['categoryId'])) {
            $query->orWhere([ 'in', 'category_id', $conf['categoryId']]);
        }
        $rows = $query->all();
        foreach ($rows as $product) {
            $result[] = $product['id'];
        }
        return $result;
    }


    private function renderWizard($mainContent)
    {
        return $this->render('wizard', [
            'wizMain'    => $mainContent,
            'wizSummary' => $this->buildWizSummary(),
        ]);
    }
    /**
     * Builds and returns the HTML to render the summary area of the wizard
     * 
     * The summary area contains information describing already provided data. It
     * is display on the rigt side of the wizard
     *
     * @return string HTML of the summary view
     */
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

        if ($session->has(self::SESS_PRODUCTS) && $session->has(self::SESS_ORDERS)) {            
            $products = $session[self::SESS_PRODUCTS];
            $data['orders'] = array_map(function($order) use ($products) {
                $foundProduct = null;
                foreach ($products as $product) {
                    if ($product['id'] == $order['product_id']) {
                        $foundProduct = $product;
                        break;
                    }
                }
                if ( $foundProduct !== null) {
                    $order['product_name'] = $foundProduct['name'];
                } else {
                    $order['product_name'] = '??????';
                }
                return $order;
            },  $session[self::SESS_ORDERS]);
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

        $contact = new Contact([
            'is_natural_person' => true
        ]);
        

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
            return $this->redirect(['contact-edit']);
        } else {
            $this->cleanSessionData();
        }

        return $this->renderWizard(
            $this->renderPartial('_contact-search')
        );
    }

    public function actionContactEdit()
    {
        if (!Yii::$app->session->has(self::SESS_CONTACT)) {
            // session variable is not as expected
            return $this->redirect(['contact-search']);
        }
        //Yii::$app->session->remove(self::SESS_ADDRESS);

        $model = Contact::create();
        $model->setAttributes(Yii::$app->session[self::SESS_CONTACT], false);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // save contact to session
            Yii::$app->session[$this::SESS_CONTACT] = $model->getAttributes();
            if ( empty($model->address_id)) {
                // contact has no address : add one
                return $this->redirect(['address-search']);
            } else {
                // contact has address : review
                $address = $model->address;
                Yii::$app->session[self::SESS_ADDRESS] = $address->getAttributes(); 
                return $this->redirect(['address-edit']);                
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
    
            // build the web service result set
            $wsResult = array_map(function($item){
                $property = $item['properties'];
                return [
                    'id'       => null, // by convention id = null for  WS result items
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

        // build the DB result set
        $dbResult = array_map(function($row){
            return [
                'id'       => $row['id'],
                'address'  => $row['line_1'],
                'city'     => $row['city'],
                'zip_code' => $row['zip_code'],
                'country'  => $row['country'],
                'urlView'  => Url::to(['/address/view', 'id' => $row['id']])
            ];
        }, $dbRows);

        // done : return response
        return $dbResult + $wsResult;
    }

    // TODO: if contact is already in DB, then it may already be linked to an address
    // so we should check for existing address
    public function actionAddressSearch()
    {
        if (!Yii::$app->session->has(self::SESS_CONTACT)) {
            // session variable is not as expected
            return $this->redirect(['contact-search']);
        }
        Yii::$app->session->remove(self::SESS_ADDRESS);
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
            return $this->redirect(['address-edit']);
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
            return $this->redirect(['address-search']);
        }

        $model = new Address(Yii::$app->session[self::SESS_ADDRESS]);

        if (Yii::$app->request->isPost) {
            $action = Yii::$app->request->post('action');
  
            switch ($action) {
                case 'reset-form':
                    //$model = new Address();
                    Yii::$app->session->remove(self::SESS_ADDRESS);
                    return $this->redirect(['address-search']);
                    break;
                
                default:
                    if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                        Yii::$app->session[self::SESS_ADDRESS] = $model->getAttributes();
                        return $this->redirect(['product-select']);
                    }  
                    break;
            }
        }

        $contact = new Contact(Yii::$app->session[self::SESS_CONTACT]);
        $contactsSameAddress = [];
        if ( !empty($model->id)) {
            // user has choosen an address that exists in the db. Maybe it is already used
            // by contacts ? 
            foreach($model->contacts as $contactPerAdress) {
                if ($contactPerAdress->id !== $contact->id) {
                    $contactsSameAddress[] = $contactPerAdress;
                }
            }            
        }

        return $this->renderWizard(
            $this->renderPartial('_address-edit', [
                'model' => $model,
                'contact' => $contact,
                'contactsSameAddress' => $contactsSameAddress
            ])
        );
    }

    public function actionAjaxProductSearch()
    {
        if (!Yii::$app->request->isAjax) {
            throw new NotFoundHttpException('invalid input');
        }

        // read query and validate params
        $productName = Yii::$app->request->get('name');  // mandatory
        if (empty($productName)) {
            throw new yii\web\BadRequestHttpException('the parameter "name" is missing');
        }

        // preparing the response format
        Yii::$app->response->format = Response::FORMAT_JSON; 

        // searching product in the DB
        $query = Product::find()
            ->where(['LIKE', 'name', $productName])
            ->asArray();

        $product_group_2 = ProductSelectionForm::getProductIdsByGroup(ProductSelectionForm::GROUP_2);        
        if ( count($product_group_2) > 0) {
            $query->andWhere(['IN', 'id', $product_group_2 ]);
        }

        // done : return response
        return $query->all();
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
            return $this->redirect(['address-search']);
        }
        //Yii::$app->session->remove(self::SESS_PRODUCTS);

        $model = new ProductSelectionForm();
        
        $firstClassProductIds = ProductSelectionForm::getProductIdsByGroup(ProductSelectionForm::GROUP_1); 
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
                Yii::$app->session->remove(self::SESS_ORDERS);
                return $this->redirect(['order']);
            }
        }

        // prepare to render the view
        // we need the producrt id => name map only for product rendered as checkbox list
        $rows = \app\models\Product::find()
            ->where(['in', 'id', $firstClassProductIds])
            ->asArray()
            ->all();        
        $firstClassProductIndex = ArrayHelper::map($rows, 'id', 'name'); // [ productId => productName]

        $products_2 = $model
            ->querySelectedProductModels(ProductSelectionForm::GROUP_2)
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
            return $this->redirect(['product-select']);
        }

        $orderModels = [];
        $products = []; // for rendering only

        // load orders from products selected in the previous step
        $fromContactID = \app\components\SessionContact::getContactId();

        foreach (Yii::$app->session[self::SESS_PRODUCTS] as $product) {
            
            // compute date range values
            $dateStart = SessionDateRange::getStart();
            if (!empty($product['valid_date_start'])) {
                $dateStart = $product['valid_date_start'];
            }
            $dateEnd = SessionDateRange::getEnd();
            if (!empty($product['valid_date_end'])) {
                $dateEnd = $product['valid_date_end'];
            }
            $order = new Order([
                'product_id'       => $product['id'],
                // by CONVENTION : because the contact may be new and so, doesn't have any id,
                // we temporary set the to_contact_id with the same value as the from_contact_id set the
                // This WILL NEED TO BE UPDATED when registration is submited
                'to_contact_id'    => $fromContactID,
                'from_contact_id'  => $fromContactID,
                'value'            => $product['value'],
                'valid_date_start' => DateHelper::toDateAppFormat($dateStart),
                'valid_date_end'   => DateHelper::toDateAppFormat($dateEnd),
            ]);
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
                return $this->redirect(['transaction']);
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
                'orderModels'     => $orderModels,
                'products'        => $products,
                'orderTotalValue' => $orderTotalValue
            ])
        );
    }

    public function actionTransaction()
    {
        // orders MUST be present in the session
        if (!Yii::$app->session->has(self::SESS_ORDERS)) {
            return $this->redirect(['order']);
        }

        // compute order total value (for later use)
        $orders = Yii::$app->session[self::SESS_ORDERS];
        $orderTotalValue = 0;
        foreach ($orders as $order) {
            $orderTotalValue += $order['value'];
        }

        // select the from_account_id attribute
        // If the contact exists and has more than one bank_account we must ask which one to use
        $contact = new Contact();
        $contact->setAttributes(Yii::$app->session[self::SESS_CONTACT] , false);
        $fromAccounts = $contact->bankAccounts;

        // holds the list of transactions
        $transactionModels = [];

        if (Yii::$app->request->isGet) {
            // displaying the form : initialize the transaction list with one transaction
            // to cover all order(s) value
            $transactionModels[] = $this->createDefaultTransaction([
                'value' => $orderTotalValue
            ]);
        } else {
            // POST request : user submit the form fot save or ad/remove transactions

            // populate transactions model list : 
            // 1. create all as empty models 
            $trForms = Yii::$app->request->post('Transaction');
            for ($i=0; $i < count($trForms); $i++) { 
                $transactionModels[] = $this->createDefaultTransaction();
            }
            // 2. use "loadMultiple" to assign value to model attributes
            Model::loadMultiple($transactionModels, Yii::$app->request->post());
    
            // 3. force account attributes
            // from_account_id is temprary set to a value (here same as to_account_id) but
            // it will be set to its actual value on save
            foreach ($transactionModels as $transaction) {
                $transaction->from_account_id = SessionContact::getBankAccountId();
                $transaction->to_account_id   = SessionContact::getBankAccountId();
            }

            // handle submit actions : 'action' and optionally 'index'
            $action = Yii::$app->request->post('action');
            switch ($action) {
                case 'add-transaction': ////////////////////////////////////////////////////////
                    $transactionModels[] = $this->createDefaultTransaction([
                        'reference_date' => null
                    ]);
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

                        // prepare the fromAccountId
                        // default is : the contact is a new record, it doesn't have account at all so BY CONVENTION set
                        // fromAccountID with same value than toAccountId. Later on, we will update this attribute
                        $finalFromAccountId = SessionContact::getBankAccountId();
                        if (count($fromAccounts) > 1) {
                            // contact exists and has more than one account : user had to select the one to use
                            $finalFromAccountId = $fromAccountId;
                        } elseif( count($fromAccounts) === 1) {
                            // contact exists and has exactly one account : use it !
                            $finalFromAccountId = $fromAccounts[0]->id;
                        }

                        if ($isValid) {
                            // time to save to session
                            $toSave = [];
                            foreach ($transactionModels as $transaction) {
                                $transaction->from_account_id = $finalFromAccountId;
                                $toSave[] = $transaction->getAttributes();
                            }
                            \Yii::$app->session[self::SESS_TRANSACTIONS] = $toSave;
                            return $this->redirect(['commit']);
                        }
                    }
                    break;
            }
        }

        return $this->renderWizard(
            $this->renderPartial('_transaction', [
                'transactionModels' => $transactionModels,
                'orderTotalValue'   => $orderTotalValue,
                'fromAccounts'      => ArrayHelper::map($fromAccounts, 'id', 'longName'),
                'contact'           => $contact
            ])
        );
    }

    public function actionCommit()
    {
        //return $this->redirect(['transaction']);
        

        if (!Yii::$app->session->has(self::SESS_TRANSACTIONS)) {
            return $this->redirect(['transaction']);
        }        

        // let's load all models from session data ////////////////////////////////////

        $contact = new Contact(Yii::$app->session[self::SESS_CONTACT]);
        $address = new Address(Yii::$app->session[self::SESS_ADDRESS]);
        $orders = array_map(function($orderAttr){
            return new Order($orderAttr);
        },Yii::$app->session[self::SESS_ORDERS]);
        $transactions = array_map(function($transactionAttr){
            return new Transaction($transactionAttr);
        },Yii::$app->session[self::SESS_TRANSACTIONS]);

        
        // save/update /////////////////////////////////////////////////////////////////

        if (true) {
            // address ----------
            // begin with address because contact has a col that points to the address record
            $isNewAddress = null;
            if( !empty($address->id)) {
                // address already in DB : update
                $dbAddress = Address::findOne($address->id);
                $dbAddress->setAttributes($address->getAttributes(), false);
                $dbAddress->save();
                $address = $dbAddress;
                $isNewAddress = false;
            } else {
                // new address : insert
                $address->save();   // insert
                $isNewAddress = true;
            }
        
            // contact -------------
            $lazyFromAccountId = null;
            $isNewContact = null;
            $contact->address_id = $address->id;
            if (!empty($contact->id)) {
                // contact already in DB : update
                $dbContact = Contact::findOne($contact->id);
                $dbContact->setAttributes($contact->getAttributes(), false);
                $dbContact->save(); // update
                $contact = $dbContact;
                $isNewContact = false;
            } else {
                // contact is new
                $contact->save();   // insert
                $isNewContact = true;

                // create default bank account
                $bankAccount = new \app\models\BankAccount();
                $bankAccount->contact_id = $contact->id;
                $bankAccount->name = '';
                $bankAccount->save(false);  // insert
                $lazyFromAccountId = $bankAccount->id;            
            }

            $transactionValueTotal = 0;
            foreach ($transactions as  $transaction) {
                $transactionValueTotal += $transaction->value;
            }
            $orderValueTotal = 0;
            foreach ($orders as $order) {
                $orderValueTotal += $order->value;
            }

            // transactions ------------------
            foreach ($transactions as  $transaction) {
                // set account id
                // by CONVENTION, in the actionTransaction when a contact is new, the from_account_id is set
                // with the same value as the to_account_id. Now thta tyhe contact has bee ninserted in DB and
                // the bank account created, update from_account_id
                if ($transaction->from_account_id == $transaction->to_account_id) {
                    // lazy assignement : the contact has been created just now, it's bank account id
                    // was not known until  then but now we know it !
                    $transaction->from_account_id = $lazyFromAccountId;
                }
                $transaction->orders_value_total = $orderValueTotal;
                $transaction->save();
            }

            // orders --------------------------
            foreach ($orders as $order) {
                $order->to_contact_id = $contact->id;
                $order->transactions_value_total = $transactionValueTotal;
                $order->save();

                foreach ($transactions as $transaction) {
                    $order->link('transactions', $transaction);
                }
            }        

            // clean up session data
            $this->cleanSessionData();
        }

        // render result
        return $this->render('commit', [
            'contact'      => $contact,
            'address'      => $address,
            'orders'       => $orders,
            'transactions' => $transactions
        ]);
    }

    private function cleanSessionData() 
    {
        Yii::$app->session->remove(self::SESS_CONTACT);
        Yii::$app->session->remove(self::SESS_ADDRESS);
        Yii::$app->session->remove(self::SESS_PRODUCTS);
        Yii::$app->session->remove(self::SESS_ORDERS);
        Yii::$app->session->remove(self::SESS_TRANSACTIONS);        
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
}
