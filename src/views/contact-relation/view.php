<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ContactRelation */

$title = $model->sourceContact->longName 
    . ' - '
    . $model->targetContact->longName;
$titlePage = Html::encode($model->sourceContact->longName) 
    . ' <span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span> ' 
    . Html::encode($model->targetContact->longName);

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Contacts', 'url' => ['contact/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Relations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="contact-relation-view">

    <h1>Relation <small><?= $titlePage ?></small></h1>
    <hr/>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a(Yii::t('app', 'Create Another Contact Relation'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'source_contact_id',
                'label'     => 'Source Contact',
                'format'    => 'raw',
                'value'     => function ($model) {
                    return Html::a(
                        '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> ' .
                            Html::encode($model->sourceContact->longName),
                        ['contact/view','id'=>$model->source_contact_id],
                        [ 'title' => 'view contact' ]
                    );
                }
            ],
            [
                'attribute' => 'type',
                'value'     => function ($model) {
                    return app\components\Constant::getContactRelationName($model->type);
                }
            ],
            [
                'attribute' => 'target_contact_id',
                'label'     => 'Source Contact',
                'format'    => 'raw',
                'value'     => function ($model) {
                    return Html::a(
                        '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> ' .
                            Html::encode($model->targetContact->longName),
                        ['contact/view','id'=>$model->target_contact_id],
                        [ 'title' => 'view contact' ]
                    );
                }
            ],
            'valid_date_start:appDate',
            'valid_date_end:appDate',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
