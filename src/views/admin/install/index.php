<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
?>
<div>
    <h1>Install</h1>
    <hr/>

    <?php if ($success): ?>
        <div class="alert alert-success" role="alert">
            <b>Success</b> 
        </div>
    <?php else: ?>
        <div class="alert alert-danger" role="alert">
            <b>Error!</b>
            <?php if ($adminUser->hasErrors()) {
                    foreach ($adminUser->getErrors() as $key => $value) {
                        echo $value;
                    }
                }
            ?>
        </div>
    <?php endif; ?>

</div>
