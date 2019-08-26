<?php

namespace app\controllers\api;


class BaseAPIController extends \yii\rest\ActiveController
{
    public function checkAccess($action, $model = null, $params = [])
    {
        // check if the user can access $action and $model
        // throw ForbiddenHttpException if access should be denied
        if (\Yii::$app->user->isGuest) {
            throw new \yii\web\ForbiddenHttpException('user not authenticated');
        }
    }
}
