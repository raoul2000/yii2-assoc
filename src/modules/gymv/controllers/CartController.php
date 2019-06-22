<?php

namespace app\modules\gymv\controllers;

use Yii;
use app\models\Product;
use app\models\ProductSearch;
use app\models\Order;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\SessionDateRange;
use yii\web\Response;
use app\modules\gymv\models\Cart;

class CartController extends \yii\web\Controller
{
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

    public function actionCheckOut($action = '', $id = null)
    {
        $cart = new Cart();
        $selectedProductId = $cart->getProductIds();

        if (count($selectedProductId) == 0) {
            Yii::$app->session->setFlash('error', 'no item selected');
            return $this->actionIndex();
        }

        // validate selected products ids
        $selectedProduct = Product::findAll($selectedProductId);
        if (count($selectedProduct) != count($selectedProductId)) {
            throw new NotFoundHttpException('One or more product are not found.');
        }

        $orders = [];
        foreach ($selectedProduct as $product) {
            $order = new Order();
            $order->product_id = $product->id;
            $order->value = $product->value;
            $orders[] = $order;
        }

        if (Yii::$app->request->isGet && !empty($action)) {
            switch($action) {
                case 'remove-order':
                    if (!isset($id)) {
                        throw new NotFoundHttpException('invalid action request');
                    }
                    $cart->removeProductIds([$id]);
                    $orders = array_filter($orders, function($order) use ($id) {
                        return $order->id != $id;
                    });
                break;
                default:
                    throw new NotFoundHttpException('invalid request');
            }
        }

        return $this->render('check-out', [
            'orders' => $orders,
            'products' => \app\models\Product::getNameIndex(),
            'contacts' => \app\models\Contact::getNameIndex()
        ]);
    }

    public function actionManage()
    {
        $orders = [];
        if (Yii::$app->request->isGet) {
            // convert selected products into orders and clear cart
            $cart = new Cart();
            $selectedProductId = $cart->getProductIds();
            if( count($selectedProductId) != 0) {
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

        // prepare order session storage
        $session = Yii::$app->session;
        if (!$session->has('cart')) {
            $session['cart'] = [
                'orders' => [],
                'transactions' => []
            ];
        }

        // load order models from session storage
        $orders = array_map(function($orderAttr){
            $order = new Order();
            $order->setAttributes($orderAttr);
            return $order;
        }, array_merge($session['cart']['orders'], $orders));

        Order::loadMultiple($orders,  Yii::$app->request->post());

        // process actions
        $action = Yii::$app->request->post('action');
        
        switch($action) {
            case 'add-order':
                $newOrder = new Order();
                $orders[] = $newOrder;
            break;
            default:
                throw new NotFoundHttpException('invalid request');
        }

        // save models back to session storage
        $session['cart'] = [
            'orders' => array_map(function($order) {
                return $order->getAttributes();
            },$orders),
            'transactions' => []
        ];

        return $this->render('manage', [
            'orders' => $orders,
            'products' => \app\models\Product::getNameIndex(),
            'contacts' => \app\models\Contact::getNameIndex()
        ]);
    }
}
