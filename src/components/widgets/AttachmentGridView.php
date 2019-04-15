<?php

namespace app\components\widgets;

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;

class AttachmentGridView extends GridView
{
    public function init()
    {
        $this->tableOptions = ['class' => 'table table-hover table-condensed'];
        $this->layout = '{items}';
        $this->columns = [
            'name',
            'note',
            [
                'attribute' => 'updated_at',
                'format' => ['date', 'php:d/m/Y H:i']
            ],
            [
                'class'     => 'yii\grid\ActionColumn',
                'template'  => '{preview} {update} {download} {delete} ',
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action == 'delete') {
                        return Url::to(['delete-attachment', 'id' => $model->id, 'redirect_url' => Url::current() ]);
                    } elseif ($action == 'download') {
                        return Url::to(['download-attachment', 'id' => $model->id]);
                    } elseif ($action == 'preview') {
                        return Url::to(['preview-attachment', 'id' => $model->id]);
                    } elseif ($action == 'update') {
                        return Url::to(['update-attachment', 'id' => $model->id, 'redirect_url' => Url::current()]);
                    }
                    return '';
                },
                'buttons'   => [
                    'download' => function ($url, $attachment, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-download-alt"></span>',
                            $url,
                            ['title' => 'download', 'data-pjax'=>0]
                        );
                    },
                    'preview' => function ($url, $attachment, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"></span>',
                            $url,
                            ['title' => 'preview in a new window', 'target' => '_blank', 'data-pjax' => 0]
                        );
                    },
                ]
            ]
        ];
        parent::init();
    }
}
