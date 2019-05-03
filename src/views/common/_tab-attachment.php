<?php

use yii\helpers\Html;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;

$allAttachments = $model->attachments;
?>

<p>
    <?= Html::a('Add Attachment', ['create-attachment', 'id' => $model->id, 'redirect_url' => Url::current() ], ['class' => 'btn btn-primary']) ?>
</p>
<?php if (count($allAttachments) == 0): ?>
    no attachment
<?php else: ?>
    <?= \app\components\widgets\AttachmentGridView::widget([
        'dataProvider' => new ArrayDataProvider(['allModels' => $allAttachments]),            
    ]) ?>
<?php endif; ?>