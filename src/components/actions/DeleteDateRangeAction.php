<?php
namespace app\components\actions;

use Yii;
use yii\base\Action;
use app\components\Constant;

class DeleteDateRangeAction extends Action
{
    public function run($redirect_url)
    {
        Yii::$app->session->remove(Constant::SESS_PARAM_NAME_DATERANGE);
        return $this->controller->redirect($redirect_url);        
    }
}