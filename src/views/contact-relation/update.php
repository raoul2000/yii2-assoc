<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ContactRelation */
$title = $model->sourceContact->longName 
    . ' - '
    . $model->targetContact->longName;

$titlePage = Html::encode($model->sourceContact->longName) 
    . ' <span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span> ' 
    . Html::encode($model->targetContact->longName);


$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Contacts', 'url' => ['contact/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Relations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="contact-relation-update">

    <h1>
        Update Contact Relation
        <small>
            <?= $titlePage ?>
        </small>
    </h1>
    <hr/>

    <?= $this->render('_form', [
        'model' => $model,
        'contacts' => $contacts,
        'contactRelationTypes' => $contactRelationTypes
    ]) ?>

</div>
