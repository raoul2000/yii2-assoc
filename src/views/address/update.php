<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Address */

$this->title = 'Update Address';
$this->params['breadcrumbs'][] = ['label' => 'Addresses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';

?>
<div class="address-update">

    <h1>
        <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
        <?= Html::encode($this->title) ?>
        <?php if (isset($contact)): ?>
            <small>
                for <b><?= Html::encode($contact->name) ?></b>.
            </small>
        <?php endif; ?>
    </h1>

    <hr/>
    
    <?php if (count($otherContacts) != 0):?>
        <?php if (isset($contact)) {
            $icon = 'glyphicon-warning-sign';
            $className = 'alert-warning';
            $text = 'This address is also used by';
        } else {
            $icon = 'glyphicon-info-sign';
            $className = 'alert-info';
            $text = 'This address is used by';
        }?>
        <div class="alert <?= $className ?>" role="alert">
            <p>
                <span class="glyphicon <?= $icon ?>" aria-hidden="true"></span> <?= $text ?>
                <?php
                $linkedContacts = [];
                foreach ($otherContacts as $contact) {
                    $linkedContacts[] = '<b>' . Html::a(Html::encode($contact->name) , ['contact/view', 'id' => $contact->id]) . '</b>';
                }
                echo implode(', ', $linkedContacts);
                ?>
            </p>
        </div>
    <?php endif; ?>

    <?= $this->render('_form', [
        'model' => $model,
        'redirect_url' => $redirect_url
    ]) ?>

</div>
