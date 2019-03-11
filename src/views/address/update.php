<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Address */

$this->title = 'Update Address: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Addresses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';

?>
<div class="address-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr/>
    
    <?php if (count($model->contacts) != 0):?>
        <div class="alert alert-warning" role="alert">
            <p>
                <span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span> This address is used by :
                
                <?php
                $linkedContacts = [];
                foreach ($model->contacts as $contact) {
                    $linkedContacts[] = '<b>' . Html::a(Html::encode($contact->name) , ['contact/view', 'id' => $contact->id]) . '</b>';
                }
                echo implode(', ', $linkedContacts);
                ?>
            </p>
        </div>
    <?php endif; ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
