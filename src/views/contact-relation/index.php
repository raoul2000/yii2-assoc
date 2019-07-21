<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ContactRelationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Contact Relations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-relation-index">

    <h1>
        <?= Html::encode($this->title) ?>
    </h1>
    <hr/>
    <?php Pjax::begin(); ?>
    <p>
        <?= Html::a(Yii::t('app', 'Create Contact Relation'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'source_contact_id',
                'label'     => 'Source Contact',
                'filter'    => $contacts,
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    return Html::a(
                        Html::encode($model->sourceContact->longName),
                        ['contact/view','id'=>$model->source_contact_id],
                        [ 'data-pjax' => 0 ]
                    );
                }
            ],
            'type',
            [
                'attribute' => 'target_contact_id',
                'label'     => 'Target Contact',
                'filter'    => $contacts,
                'format'    => 'raw',
                'value'     => function ($model, $key, $index, $column) {
                    return Html::a(
                        Html::encode($model->targetContact->longName),
                        ['contact/view','id'=>$model->target_contact_id],
                        [ 'data-pjax' => 0 ]
                    );
                }
            ],
            'created_at',
            'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
