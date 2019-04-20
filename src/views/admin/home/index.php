<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Administration';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Administration <small>website</small></h1>
<hr/>
<p>
    <?= \app\components\widgets\DateRangeWidget::widget() ?>      
    <?= \app\components\widgets\UserContactWidget::widget() ?>      
</p>
<hr/>
<p>
    <?php if (Yii::$app->user->can('manageUser')) {
        echo Html::a('Manage Users', ['user/admin'], ['class' => 'btn btn-primary']);
    }?>
    <?= Html::a('Manage Contacts', ['contact/index'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Manage Address', ['address/index'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Manage Products', ['product/index'], ['class' => 'btn btn-primary']) ?>
    <hr/>
    <?= Html::a('Manage Bank Accounts', ['bank-account/index'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Manage Transactions', ['transaction/index'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Manage Transaction Packs', ['transaction-pack/index'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Manage Orders', ['order/index'], ['class' => 'btn btn-primary']) ?>
</p>
<hr/>
<p>
    <?= Html::a('Record History', ['/record-history'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Attachment', ['/attachment'], ['class' => 'btn btn-primary']) ?>
</p>
<hr/>
<p>
    <?= Html::a('User Admin', ['/user/admin'], ['class' => 'btn btn-danger']) ?>
    <?= Html::a('Session Admin', ['/session'], ['class' => 'btn btn-danger']) ?>
    <?= Html::a('DB Backup/Restore', ['/db-manager'], ['class' => 'btn btn-danger']) ?>
</p>
