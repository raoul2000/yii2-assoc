<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Address */

$this->title = 'Update Address: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Addresses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';

$contacts = $model->contacts;
?>
<div class="address-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr/>
    <?php if( count($model->contacts) != 0) : ?>
        <p>
            Address for <?php foreach( $model->contacts as $contact) {
                echo '<b>"' . Html::encode($contact->name) .'"</b>, ';
            }?>
        </p>
    <?php endif; ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
