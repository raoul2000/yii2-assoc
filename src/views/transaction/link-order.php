<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\web\View;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */

$this->title = 'N°' . $transaction->id;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Transactions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $transaction->id]];
$this->params['breadcrumbs'][] = \Yii::t('app', 'link Order');
\yii\web\YiiAsset::register($this);

// Client Js script
// Hanlde user click on 'unlink order' button

$urlLinkOrders = Url::toRoute(['transaction/ajax-link-orders']);
$gridViewElementId = 'unlinked-orders';
$jsScript=<<<EOS
    $('#btn-link-orders').on('click', (ev) => {
        let selectedOrderIds = $('#{$gridViewElementId}').yiiGridView('getSelectedRows');
        if( selectedOrderIds.length !== 0) {
            $.post({
                url: '{$urlLinkOrders}',
                dataType: 'json',
                data: {
                    transactionId : {$transaction->id},
                    selectedOrderIds: selectedOrderIds
                },
                success: function(data) {
                    //location.reload();
                    $.pjax.reload({container: '#pjax_{$gridViewElementId}', async: true});
                },
             });            
        } else {
            alert('No order selected');
        }
    });
EOS;

$this->registerJs($jsScript, View::POS_READY, 'transaction-order-link-handler');

?>
<p>
    <?= Html::a(
        '<span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> ' 
            . \Yii::t('app', 'Back To Transaction N°') . $transaction->id,
        ['view', 'id' => $transaction->id]
    )?>
</p>

<div class="alert alert-info" role="alert">
    <p>
        <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> 
        <?= \Yii::t('app', 'Please select one or more orders for this transaction') ?>
    </p>
</div>  

    <?php Pjax::begin(['id' => 'pjax_' . $gridViewElementId]); ?>
        <?= GridView::widget([
            'id'           => $gridViewElementId,
            'tableOptions' => ['class' => 'table table-hover table-condensed'],
            'dataProvider' => $orderDataProvider,
            'filterModel'  => $orderSearchModel,
            'columns'      => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'checkboxOptions' => function ($model) {
                        return ['value' => $model->id];
                    },
                ],
                'id',
                [
                    'attribute' => 'product_id',
                    'label'     => \Yii::t('app', 'Product'),
                    'filter'    => $products,
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column) use ($products) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-gift" aria-hidden="true"></span> '
                                . Html::encode($products[$model->product_id]),
                            ['product/view','id'=>$model->product_id],
                            [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view product') ]
                        );
                    }
                ],
                [
                    'attribute' => 'to_contact_id',
                    'label'     => \Yii::t('app', 'Beneficiary'),
                    'filter'    => $contacts,
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column) use ($contacts) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> '
                                . Html::encode($contacts[$model->to_contact_id]),
                            ['contact/view','id'=>$model->to_contact_id],
                            [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view contact') ]
                        );
                    }
                ],
                'value'
            ],
        ]); ?>
    <?php Pjax::end(); ?>
    
    <div class="form-group">
        <?= Html::Button(
            \Yii::t('app', 'Link Selected Orders'), 
            ['id' => 'btn-link-orders', 'class' => 'btn btn-success']
        )?>
    </div>
