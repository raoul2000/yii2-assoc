<?php

namespace app\modules\gymv\controllers;

use Yii;
use yii\base\Model;
use app\models\Product;
use app\models\ProductSearch;
use app\models\Order;
use app\models\Transaction;
use yii\web\Controller;
use app\models\BankAccount;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\SessionDateRange;
use yii\web\Response;
use app\modules\gymv\models\Cart;
use yii\helpers\FileHelper;

class CartController extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        if ($action->id == 'check-out') {
            Yii::$app->request->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $cart = new Cart();
        $productIdsInCart = $cart->getProductIds();
        if (count($productIdsInCart) !== 0) {
            $searchModel = new ProductSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->query->andWhere(['in', 'id', $productIdsInCart]);
        } else {
            $dataProvider = null;
        }
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAddProduct()
    {
        $cart = new Cart();
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['not in', 'id', $cart->getProductIds()]);

        return $this->render('add-product', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionAjaxAddProduct()
    {
        if (!Yii::$app->request->isAjax) {
            throw new yii\web\ForbiddenHttpException();
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();
        $productIds = $data['ids'];

        $cart = new Cart();
        $cart->addProductIds($productIds);
        $cart->save();

        return [
            'success' => true
        ];
    }

    public function actionAjaxRemoveProduct()
    {
        if (!Yii::$app->request->isAjax) {
            throw new yii\web\ForbiddenHttpException();
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();
        $productIds = $data['ids'];

        $cart = new Cart();
        $cart->removeProductIds($productIds);
        $cart->save();

        return [
            'success' => true
        ];
    }
    public function actionUpdate()
    {
        return $this->render('update');
    }

    public function actionInitManage()
    {
        $order = new Order();
        $fromBankAccountId = null;

        $transaction = new Transaction();
        $fromBankAccounts = [];

        if ($order->load(Yii::$app->request->post())) {
            // beneficiary contact is the owner of the source bank account
            $fromBankAccounts = $order->toContact->getBankAccounts()->count();
            $toBankAccounts = $order->fromContact->getBankAccounts()->count();

            $queryParams = [];
            if ($fromBankAccounts > 1) {
                $queryParams['to_contact_id'] = $order->toContact->id;
            }
            if ($toBankAccounts > 1) {
                $queryParams['from_contact_id'] = $order->fromContact->id;
            }
            if (count($queryParams) != 0 ) {
                Yii::$app->session->setFlash('warning', 'select account');
                return $this->redirect(array_merge(['select-account'], $queryParams));
            }
        }

        return $this->render('init-manage', [
            'order' => $order,
            'contacts' => \app\models\Contact::getNameIndex()
        ]);
    }

    public function actionSelectTemplate()
    {

        $templateFiles = FileHelper::findFiles(
            Yii::getAlias('@template'),
            [
                'only' => ['*.json'],
                'caseSensitive' => false
            ]
        );
        $selectedTemplate = Yii::$app->request->post('template-name'); // file name ex : 1265-66589-99878.json
        if (!empty($selectedTemplate)) {
            $templateFilepath = Yii::getAlias('@template/' . $selectedTemplate);
            $template = json_decode(file_get_contents($templateFilepath), true);
            Yii::$app->session['cart'] = [
                'orders' => $template['orders'],
                'transactions' => $template['transactions']
            ];
            return $this->redirect(['check-out']);
        }

        $templateNames = [];
        foreach ($templateFiles as $templateFilePath) {
            $template = json_decode(file_get_contents($templateFilePath));
            $templateNames[basename($templateFilePath)] = $template->name;
        }

        return $this->render('select-template', [
            'templateNames' => $templateNames,
            'notEmptyCartWarning' => Yii::$app->session->has('cart')
        ]);
    }
    /**
     * Shopping Cart Management
     *
     * @return void
     */
    public function actionCheckOut()
    {
        $orders = [];
        $transactions = [];

        if (Yii::$app->request->isGet) {
            // convert selected products into orders and clear cart
            $cart = new Cart();
            $selectedProductId = $cart->getProductIds();
            if (count($selectedProductId) != 0) {
                $selectedProduct = Product::findAll($selectedProductId);
                if (count($selectedProduct) != count($selectedProductId)) {
                    throw new NotFoundHttpException('One or more product are not found.');
                }
                foreach ($selectedProduct as $product) {
                    $order = new Order();
                    $order->product_id = $product->id;
                    $order->value = $product->value;
                    $orders[] = $order;
                }
                $cart->clearProducts();
                $cart->save();
            }
        }

        // prepare order and transaction session storage
        $session = Yii::$app->session;
        if (!$session->has('cart')) {
            // initialize empty session storage
            $session['cart'] = [
                'orders' => [],
                'transactions' => []
            ];
        } else {
            // load ORDER models from session storage
            $orders = array_map(function ($orderAttr){
                $order = new Order();
                $order->setAttributes($orderAttr);
                return $order;
            }, array_merge($session['cart']['orders'], $orders));

            // load TRANSACTION models from session storage
            $transactions = array_map(function ($transactionAttr){
                $transaction = new Transaction();
                $transaction->setAttributes($transactionAttr);
                return $transaction;
            }, $session['cart']['transactions']); // no merge needed as transaction are never stored in the cart session
        }

        // load models from user submitions
        Model::loadMultiple($orders, Yii::$app->request->post());
        Model::loadMultiple($transactions, Yii::$app->request->post());

        // process actions
        $action = Yii::$app->request->post('action');
        if (!empty($action)) {
            switch ($action) {
                case 'submit' : ////////////////////////////////////////////////////////////
                    if (count($orders) === 0) {
                        Yii::$app->session->setFlash('error', 'No order');
                    } elseif (count($transactions) == 0) {
                        Yii::$app->session->setFlash('error', 'No transactions');
                    } else {
                        $ordersAreValid = Model::validateMultiple($orders);
                        $transactionsAreValid = Model::validateMultiple($transactions);

                        if ($ordersAreValid && $transactionsAreValid) {
                            // orders value sum must be equal to transactions values sum
                            $transactionSum = $orderSum = 0;
                            foreach ($transactions as $transaction) {
                                $transactionSum += $transaction->value;
                            }
                            foreach ($orders as $order) {
                                $orderSum += $order->value;
                            }
                            // float comparaison
                            // @see https://php.net/manual/en/language.types.float.php
                            if (abs($orderSum-$transactionSum)>0.01) {
                                Yii::$app->session->setFlash('error', "Order Sum ($orderSum) and Transaction Sum ($transactionSum) don't match");
                            } else {
                                // save all here
                                foreach ($transactions as $transaction) {
                                    $transaction->save(false);
                                }
    
                                foreach ($orders as $order) {
                                    $order->save(false);
    
                                    // arbitrarly choose to link order to transaction (we could have linked transaction to order)
                                    foreach ($transactions as $transaction) {
                                        $order->link('transactions', $transaction);
                                    }
                                }
    
                                // update attribute 'orders_value_total' for each transaction 
                                foreach ($transactions as $transaction) {
                                    $transaction->updateOrdersValueTotal();
                                }
                                // update attribute 'transactions_value_total' for each order
                                foreach ($orders as $order) {
                                    $order->updateTransactionsValueTotal();
                                }
    
                                // clear cart
                                $session->remove('cart');
    
                                // and we have a success !! 
                                Yii::$app->session->setFlash('success', '' . count($orders) . ' order(s) and ' . count($transactions) . ' transaction(s) created');
    
                                // go to index
                                return $this->redirect(['index']);
                            }
                        }
                    }
                    break;
                case 'add-transaction': ////////////////////////////////////////////////////////////
                    $newTransaction = new Transaction();

                    // smart model initialization
                    $transactionsCount = count($transactions);
                    if ($transactionsCount != 0) {
                        // use the latest order to populate some attributes of the new order
                        $last = $transactions[$transactionsCount - 1];
                        $newTransaction->from_account_id = $last->from_account_id;
                        $newTransaction->to_account_id = $last->to_account_id;
                        $newTransaction->type = $last->type;
                    } 
                    // TODO : initialize attribute from last order (if exist)

                    $transactions[] = $newTransaction;
                    break;

                case 'remove-transaction': ////////////////////////////////////////////////////////
                    $indexToRemove = Yii::$app->request->post('index', null);
                    if ($indexToRemove === null) {
                        throw new NotFoundHttpException('invalid request : missing index');
                    }
                    // remove the transaction based on the index argument
                    $updatedTransactions = [];
                    foreach ($transactions as $idx => $value) {
                        if ($idx == $indexToRemove) {
                            continue;
                        }
                        $updatedTransactions[] = $value;
                    }
                    $transactions = $updatedTransactions;
                    break;

                case 'add-order': /////////////////////////////////////////////////////////////////
                    $newOrder = new Order();
                    // smart initialization
                    $ordersCount = count($orders);
                    if ($ordersCount != 0) {
                        // use the latest order to populate some attributes of the new order
                        $last = $orders[$ordersCount - 1];
                        $newOrder->from_contact_id = $last->from_contact_id;
                        $newOrder->to_contact_id = $last->to_contact_id;
                    }
                    // TODO : initialize attribute from last transaction (if exist)
                    $orders[] = $newOrder;
                    break;

                case 'remove-order': //////////////////////////////////////////////////////////////
                    $indexToRemove = Yii::$app->request->post('index', null);
                    if ($indexToRemove === null) {
                        throw new NotFoundHttpException('invalid request : missing index');
                    }
                    // remove the order based on the index argument
                    $updatedOrders = [];
                    foreach ($orders as $idx => $value) {
                        if ($idx == $indexToRemove) {
                            continue;
                        }
                        $updatedOrders[] = $value;
                    }
                    $orders = $updatedOrders;
                    break;
                case 'reset': ///////////////////////////////////////////////////////////
                    $transactions = $orders = [];
                    break;
                case 'remove-order': //////////////////////////////////////////////////////////////
                    $indexToRemove = Yii::$app->request->post('index', null);
                    if ($indexToRemove === null) {
                        throw new NotFoundHttpException('invalid request : missing index');
                    }
                    // remove the order based on the index argument
                    $updatedOrders = [];
                    foreach ($orders as $idx => $value) {
                        if ($idx == $indexToRemove) {
                            continue;
                        }
                        $updatedOrders[] = $value;
                    }
                    $orders = $updatedOrders;
                    break;
                case 'save-as-template': ///////////////////////////////////////////////////////////
                    if (!Yii::$app->request->isAjax) {
                        throw new \yii\web\ForbiddenHttpException();
                    }
                    $templateName = Yii::$app->request->post('template-name');
                    if (empty($templateName)) {
                        throw new \yii\web\BadRequestHttpException('template name is missing');
                    }
                    // filename has random unique name - it is stored in @template folder : alias MUST be defined
                    // and target folder MUST exist
                    $uuid = \thamtech\uuid\helpers\UuidHelper::uuid();
                    $filepath = Yii::getAlias('@template/' . $uuid . '.json');
                    $data = [
                        'name' => $templateName,
                        'orders' => array_map(function ($item) {
                            return $item->getAttributes();
                        }, $orders),
                        'transactions' => array_map(function ($item) {
                            return $item->getAttributes();
                        }, $transactions),
                    ];
                    file_put_contents($filepath, json_encode($data, true));
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'success' => true,
                        'filepath' => $filepath
                    ];
                    break;
                
                default: /////////////////////////////////////////////////////////////////////////
                    throw new NotFoundHttpException('invalid request');
            }
        }

        if (count($orders) != 0 || count($transactions) != 0) {
            // save models back to session storage
            $session['cart'] = [
                'orders' => array_map(function ($order) {
                    return $order->getAttributes();
                }, $orders),
                'transactions' => array_map(function ($transaction) {
                    return $transaction->getAttributes();
                }, $transactions)
            ];
        } else {
            // clean up session storage because we have nothing selected
            $session->remove('cart');
        }
        
        // select products from DB and create additional data attributes array
        $allProducts = Product::find()->all();
        $productValues = [];
        $productOptions = [];
        foreach ($allProducts as $product) {
            $productValues[$product->id] = $product->name;
            $productOptions[$product->id] = ['data-value' => $product->value];
        }

        return $this->render('manage', [
            'orders' => $orders,
            'transactions' => $transactions,
            'bankAccounts' => BankAccount::getNameIndex(),
            'products' => $productValues,
            'productOptions' => $productOptions,
            'contacts' => \app\models\Contact::getNameIndex()
        ]);
    }
}
