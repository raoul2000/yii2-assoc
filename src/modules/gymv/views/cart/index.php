<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\web\View;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cart';
$this->params['breadcrumbs'][] = $this->title;

$submitUrl = Url::toRoute(['cart/ajax-remove-product']);
$gridViewElementId = 'cart-products';
$jsScript=<<<EOS
    $('#btn-remove-product').on('click', (ev) => {
        let selectedIds = $('#{$gridViewElementId}').yiiGridView('getSelectedRows');
        if( selectedIds.length === 0) {
            alert('No item selected');
        } else {
            $.post({
                url: '{$submitUrl}',
                dataType: 'json',
                data: {
                    ids : selectedIds
                },
                success: function(data) {
                    $.pjax.reload({container: '#pjax_{$gridViewElementId}', async: true});
                },
             });            
        }
    });
    $('#btn-checkout').on('click', (ev) => {
        document.getElementById('form-check-out').submit();
    });
EOS;

$this->registerJs($jsScript, View::POS_READY, 'remove-product-from-cart');

?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr/>

    <?php if ($dataProvider === null) :?>
        <div class="alert alert-info" role="alert">
            Your cart is empty !
        </div>
        <?= Html::a('Select Product To Add', ['add-product'], ['class' => 'btn btn-default']) ?>
    <?php else: ?>
        <p>
            <?= Html::a('Select Product To Add', ['add-product'], ['class' => 'btn btn-default']) ?>
            <?= Html::button('Remove Selected Product From Cart', ['id' => 'btn-remove-product', 'class' => 'btn btn-danger']) ?>
            <?= Html::button('Check Out', ['id' => 'btn-checkout', 'class' => 'btn btn-primary']) ?>
        </p>

        <?php Pjax::begin(['id' => 'pjax_' . $gridViewElementId]); ?>
            <?= Html::beginForm(['check-out'], 'POST', ['id' => 'form-check-out']) ?>
            <?= GridView::widget([
                'tableOptions' => ['class' => 'table table-hover table-condensed'],
                'id' => $gridViewElementId,
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'checkboxOptions' => function ($model) {
                            return ['value' => $model->id];
                        },
                    ],                      
                    'name',
                    'value',
                    'valid_date_start:date',
                    'valid_date_end:date',
                ],
            ]); ?>
            <?= Html::endForm() ?>
        <?php Pjax::end(); ?>
    <?php endif; ?>
</div>
