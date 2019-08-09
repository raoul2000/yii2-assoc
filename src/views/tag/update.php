<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Tag */

$this->title = Yii::t('app', 'Update Tag: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tags'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="tag-update">

    <h1>
        <span class="glyphicon glyphicon-tag" aria-hidden="true"></span> <?= Html::encode($this->title) ?>
    </h1>
    
    <hr/>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
