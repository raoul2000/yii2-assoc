<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ContactRelation */

$this->title = Yii::t('app', 'Create Contact Relation');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contact Relations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-relation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
