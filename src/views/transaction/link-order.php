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

$this->title = $transaction->id;
$this->params['breadcrumbs'][] = ['label' => 'Transactions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $transaction->id]];
$this->params['breadcrumbs'][] = 'link Order';
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
    <?= Html::a('Back To Transaction', ['view', 'id' => $transaction->id], ['class' => 'btn btn-default']) ?>
</p>

    <?php Pjax::begin(['id' => 'pjax_' . $gridViewElementId]); ?>
        <?= GridView::widget([
            'id' => $gridViewElementId,
            'dataProvider' => $orderDataProvider,
            'filterModel' => $orderSearchModel,
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'checkboxOptions' => function ($model) {
                        return ['value' => $model->id];
                    },
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
    
    <div class="form-group">
        <?= Html::Button('Link Selected Orders', ['id' => 'btn-link-orders', 'class' => 'btn btn-success']) ?>
    </div>
