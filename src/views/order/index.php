<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \Yii::t('app', 'Orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1>
        <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
        <?= Html::encode($this->title) ?>
    </h1>
    <hr/>

    <?php 
        //Pjax::begin(); 
    ?>

    <p>
        <?= Html::a(\Yii::t('app', 'Create Order'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= \app\components\widgets\DownloadDataGrid::widget() ?>             
    </p>

    <?= GridView::widget([
        'tableOptions' => ['class' => 'table table-hover table-condensed'],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'product_id',
                'label'     => 'Product',
                'filter'    => $products,
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) use ($products) {
                    return Html::a('<span class="glyphicon glyphicon-gift" aria-hidden="true"></span> '
                            . Html::encode($products[$model->product_id]),
                        ['product/view','id'=>$model->product_id],
                        [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view product')]
                    );
                }
            ],
            [
                'attribute' => 'from_contact_id',
                'label'     => 'Provider',
                'filter'    => $contacts,
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) use ($contacts) {
                    return Html::a('<span class="glyphicon glyphicon-user" aria-hidden="true"></span> '
                            . Html::encode($contacts[$model->from_contact_id]),
                        ['contact/view','id'=>$model->from_contact_id],
                        [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view contact')]
                    );
                }
            ],
            [
                'attribute' => 'to_contact_id',
                'label'     => 'Beneficiary',
                'filter'    => $contacts,
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) use ($contacts) {
                    return Html::a('<span class="glyphicon glyphicon-user" aria-hidden="true"></span> '
                            . Html::encode($contacts[$model->to_contact_id]),
                        ['contact/view','id'=>$model->to_contact_id],
                        [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view contact')]
                    );
                }
            ],
            'value',
            'valid_date_start:appDate',
            'valid_date_end:appDate',
            'transactionValuesDiff:transactionValuesDiff',
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['nowrap' => 'nowrap']
            ],
        ],
    ]); ?>
    <?php 
        // Pjax::end(); 
    ?>
</div>
