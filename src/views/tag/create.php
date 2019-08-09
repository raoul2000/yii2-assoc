<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Tag */

$this->title = Yii::t('app', 'Create Tag');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tags'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tag-create">

    <h1>
        <span class="glyphicon glyphicon-tag" aria-hidden="true"></span> <?= Html::encode($this->title) ?>
    </h1>
    
    <hr/>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
