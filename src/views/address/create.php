<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Address */

$this->title = 'Create Address';
$this->params['breadcrumbs'][] = ['label' => 'Addresses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="address-create">

    <h1>
        <?= Html::encode($this->title) ?>
        <?php if( isset($contact)) : ?>
            <small>
                for <b><?= Html::encode($contact->name) ?></b>.
            </small>
        <?php endif; ?>
    </h1>
    
    <hr/>

    <?= $this->render('_form', [
        'model' => $model,
        'contact' => $contact,
        'redirect_url' => $redirect_url
    ]) ?>

</div>
