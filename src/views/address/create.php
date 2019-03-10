<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Address */

$this->title = 'Create Address';
$this->params['breadcrumbs'][] = ['label' => 'Addresses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="address-create">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr/>
    <?php if( isset($contact)) : ?>
        <p>
            Creating Address for <b><?= Html::encode($contact->name) ?></b>.
        </p>
    <?php endif; ?>

    <?= $this->render('_form', [
        'model' => $model,
        'contact' => $contact
    ]) ?>

</div>
