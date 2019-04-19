<?php
namespace app\components\actions;

use Yii;
use yii\base\Action;
use app\components\Constant;

class DeleteDateRangeAction extends Action
{
    public function run($redirect_url)
    {
        \app\components\SessionQueryParams::clearDateRange();
        return $this->controller->redirect($redirect_url);
    }
}
