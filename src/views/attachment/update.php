<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Attachment */

$this->title = \Yii::t('app', 'Update Attachment') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Attachments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = \Yii::t('app', 'Update');
?>
<div class="attachment-update">

    <h1>
        <span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span> 
        <?= Html::encode($this->title) ?>
    </h1>
    <hr/>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
