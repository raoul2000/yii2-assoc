<?php
namespace app\components\actions;

use Yii;
use yii\base\Action;
use app\models\forms\DateRangeForm;
use yii\web\NotFoundHttpException;
use app\components\Constant;
use app\components\SessionDateRange;
use yii\helpers\BaseUrl;
use \app\components\helpers\DateHelper;

class DateRangeAction extends Action
{
    /**
     * Set or clear date range
     *
     * @param string $redirect_url
     * @param integer $clear
     * @return void
     */
    public function run($redirect_url = null, $clear = 0)
    {
        // by default redirect to home page
        $redirect_url = ($redirect_url !== null ? $redirect_url : BaseUrl::home());

        // request to clear date range
        if ($clear == 1) {
            SessionDateRange::clearDateRange();
            return $this->controller->redirect($redirect_url);
        }

        // set date range
        $model = new DateRangeForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            SessionDateRange::setDateRange(
                DateHelper::toDateDBFormat($model->start),
                DateHelper::toDateDBFormat($model->end),
                $model->configuredDateRangeId
            );
            return $this->controller->redirect($redirect_url);
        }

        if (empty($model->start)) {
            $model->start = DateHelper::toDateAppFormat(SessionDateRange::getStart());
        }
        if (empty($model->end)) {
            $model->end =  DateHelper::toDateAppFormat(SessionDateRange::getEnd());
        }

        return $this->controller->render('/common/date-range', [
            'model' => $model,
            'configuredDateRanges' => $model->getConfiguredDateRanges(),
            'redirect_url' => $redirect_url
        ]);
    }
}
