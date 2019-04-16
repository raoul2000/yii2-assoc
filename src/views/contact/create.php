<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */

$this->title = 'Create Contact';
$this->params['breadcrumbs'][] = ['label' => 'Contacts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-create">

    <h1><span class="glyphicon glyphicon-user" aria-hidden="true"></span> <?= Html::encode($this->title) ?></h1>
    <hr/>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
