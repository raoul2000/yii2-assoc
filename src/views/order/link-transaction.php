<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */

$this->title = $order->id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $order->id]];
$this->params['breadcrumbs'][] = 'link Order';
\yii\web\YiiAsset::register($this);
?>

<?php if ($transactionDataProvider->totalCount == 0): ?>

    <p>No transaction available : they are all linked to this order. </p>
    <?= Html::a('Back To Order', ['view', 'id' => $order->id], ['class' => 'btn btn-default']) ?>

<?php else : ?>

    <?php Pjax::begin(); ?>

        <?= GridView::widget([
            'dataProvider' => $transactionDataProvider,
            'filterModel' => $transactionSearchModel,
            'columns' => [
                [
                    'class' 	=> 'yii\grid\ActionColumn',
                    'template' 	=> '{select}',
                    'buttons'   => [
                        'select' => function ($url, $transaction, $key) use ($order) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-ok"></span>', 
                                ['link-transaction', 'id'=> $order->id, 'transaction_id' => $transaction->id],
                                ['title' => 'select this transaction', 'data-pjax'=>0]
                            );
                        },
                    ]
                ],			
                'id',            
            ],
        ]); ?>
    <?php Pjax::end(); ?>

<?php endif; ?>
