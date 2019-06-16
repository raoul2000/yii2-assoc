<?php

namespace app\modules\gymv\controllers;

use Yii;
use app\models\Product;
use app\models\ProductSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\SessionDateRange;
use yii\web\Response;
use app\modules\gymv\models\Cart;
use app\models\Product;


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

    public function actionCheckOut()
    {
        $selectedProductId = Yii::$app->request->post('selection'); // array
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
        foreach( $selectedProduct as $product) {
            $order = new Order();
            $order->product_id = $product->id;
            $order->value = $product->value;
            $orders[] = $order;
        }

        return $this->render('check-out', [
            'orders' => $orders,
            'contacts' => \app\models\Contact::getNameIndex()
        ]);
    }
}
