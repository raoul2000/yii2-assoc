<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\web\View;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Add Products';
$this->params['breadcrumbs'][] = ['label' => 'Cart', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$submitUrl = Url::toRoute(['cart/ajax-add-product']);
$gridViewElementId = 'available-products';
$jsScript=<<<EOS
    $('#btn-add-product').on('click', (ev) => {
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
EOS;

$this->registerJs($jsScript, View::POS_READY, 'add-product-to-cart');

?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr/>

    <p>
        <?= Html::a('View Cart', ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::button('Add Selected Product To Cart', ['id' => 'btn-add-product', 'class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax_' . $gridViewElementId]); ?>
        <?= GridView::widget([
            'tableOptions' => ['class' => 'table table-hover table-condensed'],
            'id' => $gridViewElementId,
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
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
    <?php Pjax::end(); ?>
</div>
