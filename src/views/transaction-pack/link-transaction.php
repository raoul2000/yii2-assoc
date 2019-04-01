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


$this->title = 'Link Transaction Pack: ' . $transactionPack->name;
$this->params['breadcrumbs'][] = ['label' => 'Transaction Packs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $transactionPack->name, 'url' => ['view', 'id' => $transactionPack->id]];
$this->params['breadcrumbs'][] = 'link Transaction';
\yii\web\YiiAsset::register($this);

// Client Js script 
// Hanlde user click on 'unlink transaction' button 
$urlLinkTransaction = Url::toRoute(['transaction-pack/ajax-link-transactions']);
$gridViewElementId = 'unlinked-transactions';
$jsScript=<<<EOS
    $('#btn-link-transactions').on('click', (ev) => {
        let selectedTransactionIds = $('#{$gridViewElementId}').yiiGridView('getSelectedRows');
        if( selectedTransactionIds.length !== 0) {
            $.post({
                url: '{$urlLinkTransaction}',
                dataType: 'json',
                data: {
                    transactionPackId : {$transactionPack->id},
                    selectedTransactionIds: selectedTransactionIds
                },
                success: function(data) {
                    //location.reload();
                    $.pjax.reload({container: '#pjax_{$gridViewElementId}', async: true});
                },
             });            
        } else {
            alert('No Transaction selected');
        }
    });
EOS;

$this->registerJs($jsScript, View::POS_READY, 'transaction-pack-link-handler');

?>
<p>
    <?= Html::a('Back To Transaction Pack', ['view', 'id' => $transactionPack->id], ['class' => 'btn btn-default']) ?>
</p>

    <?php Pjax::begin(['id' => 'pjax_' . $gridViewElementId]); ?>
        <?= GridView::widget([
            'id' => $gridViewElementId,
            'dataProvider' => $transactionDataProvider,
            'filterModel' => $transactionSearchModel,
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'checkboxOptions' => function ($model) {
                        return ['value' => $model->id];
                    },
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
                'code',
                'value',
                'description',
                'is_verified:boolean',
                'reference_date:date',
            ],
        ]); ?>
    <?php Pjax::end(); ?>
    
    <div class="form-group">
        <?= Html::Button('Link Selected Transaction(s)', ['id' => 'btn-link-transactions', 'class' => 'btn btn-success']) ?>
    </div>
