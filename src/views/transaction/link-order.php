<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */

$this->title = $transaction->id;
$this->params['breadcrumbs'][] = ['label' => 'Transactions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $transaction->id]];
$this->params['breadcrumbs'][] = 'link Order';
\yii\web\YiiAsset::register($this);
?>
<p>
    <?= Html::a('Back To Transaction', ['view', 'id' => $transaction->id], ['class' => 'btn btn-default']) ?>
</p>
<?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $orderDataProvider,
        'filterModel' => $orderSearchModel,
        'columns' => [
            [
                'class'     => 'yii\grid\ActionColumn',
                'template'  => '{select}',
                'buttons'   => [
                    'select' => function ($url, $order, $key) use ($transaction) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-ok"></span>',
                            ['link-order', 'id'=> $transaction->id, 'order_id' => $order->id],
                            ['title' => 'select this order', 'data-pjax'=>0]
                        );
                    },
                ]
            ],
            'id',
            [
                'attribute' => 'product_id',
                'label'     => 'Product',
                'filter'    => $products,
                'format'    => 'html',
                'value'     => function ($model, $key, $index, $column) use ($products) {
                    return Html::a(Html::encode($products[$model->product_id]), ['product/view','id'=>$model->product_id]);
                }
            ],
            [
                'attribute' => 'contact_id',
                'label'     => 'Beneficiary',
                'filter'    => $contacts,
                'format'    => 'html',
                'value'     => function ($model, $key, $index, $column) use ($contacts) {
                    return Html::a(Html::encode($contacts[$model->contact_id]), ['contact/view','id'=>$model->contact_id]);
                }
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?>

