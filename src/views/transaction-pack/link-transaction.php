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


$longName = 'N°' . $transactionPack->id . ' - ' . $transactionPack->name;
$this->title = \Yii::t('app', 'Update Pack') . ': ' . $longName;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Transactions'), 'url' => ['transaction/index']];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Transaction Packs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $longName, 'url' => ['view', 'id' => $transactionPack->id]];
$this->params['breadcrumbs'][] = \Yii::t('app', 'link Transaction');
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
<div>
    <p>
        <?= Html::a(
            '<span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> '
            .  \Yii::t('app', 'Back To Transaction Pack'),
            ['view', 'id' => $transactionPack->id]
        )?>
    </p>
    <div class="alert alert-info" role="alert">
        <p>
            <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> 
            <?= \Yii::t('app', 'Please select one or more transactions to add to this pack.') ?>
        </p>
    </div>    


    <?php Pjax::begin(['id' => 'pjax_' . $gridViewElementId]); ?>
        <?= \Yii::t('app', 'Total Value') ?> : <b><?= $totalValue ?></b>
        <?= GridView::widget([
            'id'            => $gridViewElementId,
            'tableOptions' 	=> ['class' => 'table table-hover table-condensed'],
            'dataProvider'  => $transactionDataProvider,
            'filterModel'   => $transactionSearchModel,
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
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column) use ($bankAccounts) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-euro" aria-hidden="true"></span> '
                                . Html::encode($bankAccounts[$model->from_account_id]),
                            ['bank-account/view','id'=>$model->from_account_id],
                            [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view account')]
                        );
                    }
                ],
                [
                    'attribute' => 'to_account_id',
                    'filter'    =>  $bankAccounts,
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column) use ($bankAccounts) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-euro" aria-hidden="true"></span> '
                                . Html::encode($bankAccounts[$model->to_account_id]),
                            ['bank-account/view','id'=>$model->to_account_id],
                            [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view account')]
                        );
                    }
                ],
                [
                    'attribute' => 'type',
                    'filter'    => \app\components\Constant::getTransactionTypes(),
                    'format'    => 'raw',
                    'value'     => function ($model, $key, $index, $column) {
                        return Html::encode(\app\components\Constant::getTransactionType($model->type));
                    }
                ],
                'code',
                'value',
                'description',
                'is_verified:boolean',
                'reference_date:appDate',
            ],
        ]); ?>
    <?php Pjax::end(); ?>
    
    <div class="form-group">
        <?= Html::Button(
            \Yii::t('app', 'Link Selected Transaction(s)'), 
            ['id' => 'btn-link-transactions', 'class' => 'btn btn-success']
        )?>
    </div>
</div>