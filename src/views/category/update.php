<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Category */

$this->title = \Yii::t('app', 'Update Category') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="category-update">

    <h1>
        <span class="glyphicon glyphicon-th" aria-hidden="true"></span>
        <?= Html::encode($this->title) ?>
    </h1>
    <hr/>
    <?= $this->render('_form', [
        'model' => $model,
        //'contacts' => $contacts // categories are not private anymore
    ]) ?>

</div>
