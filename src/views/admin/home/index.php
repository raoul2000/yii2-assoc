<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Administration';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Administration <small>website</small></h1>
<hr/>
<p>
    <?= Html::a('Manage Users', ['user/admin'], ['class' => 'btn btn-primary']) ?>
</p>
