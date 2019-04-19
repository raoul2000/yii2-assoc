<?php
namespace app\components\actions;

use Yii;
use yii\base\Action;
use app\models\forms\DateRangeForm;
use yii\web\NotFoundHttpException;
use app\components\Constant;

class CreateDateRangeAction extends Action
{
    public function run($redirect_url = null)
    {
        $model = new DateRangeForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            \app\components\SessionQueryParams::setDateRange($model->start_date, $model->end_date);
            return $this->controller->redirect($redirect_url);
        }

        return $this->controller->render('/common/create-date-range', [
            'model' => $model,
            'redirect_url' => $redirect_url
        ]);
    }
}
