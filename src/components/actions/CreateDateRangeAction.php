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
            $session = Yii::$app->session;
            $session[Constant::SESS_PARAM_NAME_DATERANGE] = [
                Constant::SESS_PARAM_NAME_STARTDATE => $model->start_date,
                Constant::SESS_PARAM_NAME_ENDDATE => $model->end_date,
            ];
            return $this->controller->redirect($redirect_url);
        }

        return $this->controller->render('/common/create-date-range', [
            'model' => $model,
            'redirect_url' => $redirect_url
        ]);
    }
}
