<?php

namespace app\components\helpers;

use yii\base\InvalidCallException;
use yii\helpers\Html;

class TagHelper
{
    public static function renderTags($tagValues)
    {
        $htmlTags = array_map( function($tagValue) {
            return '<span class="label label-default">' . Html::encode($tagValue) . '</span>';
        }, $tagValues);

        return \implode(' ',$htmlTags);
    }
}