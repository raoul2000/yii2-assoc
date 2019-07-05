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

        // load models
        Model::loadMultiple($orders, Yii::$app->request->post());
        Model::loadMultiple($transactions, Yii::$app->request->post());

        // process actions
        $action = Yii::$app->request->post('action');
        if (!empty($action)) {
            switch ($action) {
                case 'submit' :
                    if (count($orders) === 0) {
                        Yii::$app->session->setFlash('error', 'No order');
                    } elseif (count($transactions) == 0) {
                        Yii::$app->session->setFlash('error', 'No transactions');
                    } else {
                        $ordersAreValid = Model::validateMultiple($orders);
                        $transactionsAreValid = Model::validateMultiple($transactions);
                        if ($ordersAreValid && $transactionsAreValid) {
                            // save all here
                            foreach ($transactions as $transaction) {
                                $transaction->save(false);
                            }

                            foreach ($orders as $order) {
                                $order->save(false);

                                // arbirarly choose to link order to transaction (we could have linked transaction to order)
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
                    break;
                case 'add-transaction': ////////////////////////////////////////////////////////////
                    $newTransaction = new Transaction();
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

                default: /////////////////////////////////////////////////////////////////////////
                    throw new NotFoundHttpException('invalid request');
            }
        }

        // save models back to session storage
        $session['cart'] = [
            'orders' => array_map(function ($order) {
                return $order->getAttributes();
            },$orders),
            'transactions' => array_map(function ($transaction) {
                return $transaction->getAttributes();
            },$transactions)
        ];
        
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
