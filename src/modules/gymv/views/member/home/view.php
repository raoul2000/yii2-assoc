<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = $contact->fullName;
$this->params['breadcrumbs'][] = ['label' => 'Members', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div id="member">
    <h1><?= Html::encode($this->title) ?></h1>
    <hr/>
    <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'tableOptions' => ['class' => 'table table-hover table-condensed'],
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'product.name',
                    'label' => 'Course',
                    'format' => 'raw',
                    'value'     => function ($model, $key, $index, $column) {
                        return Html::a( Html::encode(ucfirst($model->product->name)),
                            ['/gymv/course','OrderSearch[product_id]'=>$model->product->id],
                            [ 'data-pjax' => 0, 'title' => \Yii::t('app', 'view')]
                        );
                    }                    
                ],
            ],
        ]); ?>
    <?php Pjax::end(); ?>
</div>
