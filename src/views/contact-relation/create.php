<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ContactRelation */

$this->title = Yii::t('app', 'Create Contact Relation');
$this->params['breadcrumbs'][] = ['label' => 'Contacts', 'url' => ['contact/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Relations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-relation-create">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr/>

    <?= $this->render('_form', [
        'model' => $model,
        'contacts' => $contacts,
        'contactRelationTypes' => $contactRelationTypes
    ]) ?>

</div>
