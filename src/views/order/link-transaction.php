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

<p>
    <?= \app\components\widgets\DateRangeWidget::widget() ?>       
    <?= Html::a('Back To Order', ['view', 'id' => $order->id], ['class' => 'btn btn-default']) ?>
</p>

<?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $transactionDataProvider,
        'filterModel' => $transactionSearchModel,
        'columns' => [
            [
                'class'     => 'yii\grid\ActionColumn',
                'template'  => '{select}',
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
            [
                'attribute' => 'from_account_id',
                'filter'    => $bankAccounts,
                'format'    => 'html',
                'value'     => function ($model, $key, $index, $column) use ($bankAccounts) {
                    return Html::a(Html::encode($bankAccounts[$model->from_account_id]), ['bank-account/view','id'=>$model->from_account_id]);
                }
            ],
            [
                'attribute' => 'to_account_id',
                'filter'    =>  $bankAccounts,
                'format'    => 'html',
                'value'     => function ($model, $key, $index, $column) use ($bankAccounts) {
                    return Html::a(Html::encode($bankAccounts[$model->to_account_id]), ['bank-account/view','id'=>$model->to_account_id]);
                }
            ],
            'value',
            'description',
            'is_verified:boolean',
            'reference_date:date',
        ],
    ]); ?>
<?php Pjax::end(); ?>
