<?php
namespace app\components\actions;

use Yii;
use yii\base\Action;
use app\models\Tag;
use yii\web\NotFoundHttpException;
use yii\helpers\BaseUrl;
use yii\web\Response;

class TagListAction extends Action
{
    /**
     * Ajax action that returns a list of matching tags given a query tag
     *
     * @param string $redirect_url
     * @param integer $clear
     * @return void
     */
    public function run($query)
    {
        $models = Tag::find()
            ->where(['like', 'name',  $query])
            ->all();

        $items = [];
    
        foreach ($models as $model) {
            $items[] = ['name' => $model->name];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
    
        return $items;
    }
}
