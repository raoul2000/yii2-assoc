<?php
namespace app\components\actions;

use Yii;
use yii\base\Action;
use app\models\forms\DateRangeForm;
use yii\web\NotFoundHttpException;
use app\components\Constant;
use app\components\SessionDateRange;

class DateRangeAction extends Action
{
    public function run($redirect_url = null, $clear = 0)
    {
        // request to clear date range
        if ($clear == 1) {
            SessionDateRange::clearDateRange();
            return $this->controller->redirect($redirect_url);
        }

        // set date range
        $model = new DateRangeForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            SessionDateRange::setDateRange($model->start_date, $model->end_date);
            return $this->controller->redirect($redirect_url);
        }

        return $this->controller->render('/common/date-range', [
            'model' => $model,
            'redirect_url' => $redirect_url
        ]);
    }
}
