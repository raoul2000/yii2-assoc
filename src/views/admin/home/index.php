<?php
use yii\helpers\Html;
use \app\components\SessionContact;

/* @var $this yii\web\View */
$this->title = 'Administration';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Administration <small>website</small></h1>
<hr/>
<p>
    <?= \app\components\widgets\DateRangeWidget::widget() ?>      
    <?= \app\components\widgets\UserContactWidget::widget() ?>   
    <?= Html::a('<span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Configuration', ['config/index'], ['class' => 'btn btn-primary']) ?>
</p>    
<p>    
    <?php if (SessionContact::getContactId() != null):?>
        <?= Html::a(
            Html::encode('View Contact ' . SessionContact::getContactName()),
            ['contact/view', 'id' => SessionContact::getContactId()],
            ['class' => 'btn btn-success']
        ) ?>
    <?php endif; ?>   
    <?php if (SessionContact::getBankAccountId() != null):?>
        <?= Html::a(
            'View Bank Account',
            ['bank-account/view', 'id' => SessionContact::getBankAccountId()],
            ['class' => 'btn btn-success']
        ) ?>
    <?php endif; ?>   
</p>
<hr/>
<p>
    <?php if (Yii::$app->user->can('manageUser')) {
        echo Html::a('Manage Users', ['user/admin'], ['class' => 'btn btn-primary']);
    }?>
    <?= Html::a('<span class="glyphicon glyphicon-user" aria-hidden="true"></span> Manage Contacts', ['contact/index'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('<span class="glyphicon glyphicon-home" aria-hidden="true"></span> Manage Address', ['address/index'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Manage Products', ['product/index'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('<span class="glyphicon glyphicon-th" aria-hidden="true"></span> Manage Categories', ['category/index'], ['class' => 'btn btn-primary']) ?>
    <hr/>
    <?= Html::a('<span class="glyphicon glyphicon-euro" aria-hidden="true"></span> Manage Bank Accounts', ['bank-account/index'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('<span class="glyphicon glyphicon-transfer" aria-hidden="true"></span> Manage Transactions', ['transaction/index'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Manage Transaction Packs', ['transaction-pack/index'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Manage Orders', ['order/index'], ['class' => 'btn btn-primary']) ?>
</p>
<hr/>
<p>
    <?= Html::a('Record History', ['/record-history'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('<span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span> Attachment', ['/attachment'], ['class' => 'btn btn-primary']) ?>
</p>
<hr/>
<p>
    <?= Html::a('User Admin', ['/user/admin'], ['class' => 'btn btn-danger']) ?>
    <?= Html::a('Session Admin', ['/session'], ['class' => 'btn btn-danger']) ?>
    <?= Html::a('DB Backup/Restore', ['/db-manager'], ['class' => 'btn btn-danger']) ?>
</p>
