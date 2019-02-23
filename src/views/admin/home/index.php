<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Administration';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Administration <small>website</small></h1>
<hr/>
<p>
    <?php
        if(Yii::$app->user->can('manageUser')) {
            echo Html::a('Manage Users', ['user/admin'], ['class' => 'btn btn-primary']);
        }
    ?>
    <?= Html::a('Manage Contacts', ['contact/index'], ['class' => 'btn btn-primary']) ?>
</p>
<hr/>
<p>
    <?= Html::a('Record History', ['/record-history'], ['class' => 'btn btn-primary']) ?>
</p>
