<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Contact */

$this->title = \Yii::t('app', 'Update Contact') . ': ' . $model->longName;
$this->params['breadcrumbs'][] = ['label' => 'Contacts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->longName, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';

$subTitle = '<small>' . ($model->is_natural_person == true ? \Yii::t('app', 'Person') : \Yii::t('app', 'Organization')) . '</small>';

?>
<div class="contact-update">

    <h1>
        <span class="glyphicon glyphicon-user" aria-hidden="true"></span> 
        <?= Html::encode($this->title) ?>
        <?= $subTitle ?>
    </h1>
    
    <hr/>
    <?= $this->render('_form-' . ($model->is_natural_person == true ? 'person' : 'organization'), [
        'model' => $model,
        'cancelUrl' => $cancelUrl
    ]) ?>
</div>
