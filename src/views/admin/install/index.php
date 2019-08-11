<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
?>
<div>
    <h1>Install</h1>
    <hr/>

    <?php if ($success): ?>
        <div class="alert alert-success" role="alert">
            <h2>Success</h2>
            <?= Html::encode($message) ?>
        </div>
    <?php else: ?>
        <div class="alert alert-danger" role="alert">
            <h2>Error!</h2>
            <?= Html::encode($message) ?>
        </div>
    <?php endif; ?>
    
</div>
