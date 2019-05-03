<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */

$this->title = 'Update Contact: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Contacts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="contact-update">

    <h1>
        <span class="glyphicon glyphicon-user" aria-hidden="true"></span> 
        <?= Html::encode($this->title) ?>
    </h1>
    
    <hr/>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
