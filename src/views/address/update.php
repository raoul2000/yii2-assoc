<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Address */

$addrTitle = $model->line_1 . ' ' . $model->line_2 . ' ' . $model->line_3;
$addrTitle = substr($addrTitle, 0, 30) . '...';

$this->title = \Yii::t('app', 'Update Address');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Addresses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Html::encode($addrTitle), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = \Yii::t('app', 'Update');

?>
<div class="address-update">

    <h1>
        <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
        <?= Html::encode($this->title) ?>
        <?php if (isset($contact)): ?>
            <small>
                for <?= Html::a(
                    Html::encode($contact->longName),
                    ['contact/view' , 'id' => $contact->id],
                    ['title' => \Yii::t('app', 'view contact')]
                )?>
            </small>
        <?php endif; ?>
    </h1>

    <hr/>
    
    <?php if (count($otherContacts) != 0):?>
        <?php if (isset($contact)) {
            $icon = 'glyphicon-warning-sign';
            $className = 'alert-warning';
            $text = \Yii::t('app', 'This address is also used by');
        } else {
            $icon = 'glyphicon-info-sign';
            $className = 'alert-info';
            $text = \Yii::t('app', 'This address is used by');
        }?>
        <div class="alert <?= $className ?>" role="alert">
            <p>
                <span class="glyphicon <?= $icon ?>" aria-hidden="true"></span> <?= $text ?>
                <?php
                $linkedContacts = [];
                foreach ($otherContacts as $contact) {
                    $linkedContacts[] = '<b>' 
                        . Html::a(
                            '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> ' 
                                . Html::encode($contact->longName), 
                            ['contact/view', 'id' => $contact->id],
                            ['title' => \Yii::t('app', 'view contact')]) 
                        . '</b>';
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
