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
     * Set or clear date range
     *
     * @param string $redirect_url
     * @param integer $clear
     * @return void
     */
    public function run($query)
    {
        //$models = Tag::findAll([ 'name' => $query]);
        $models = Tag::find()
            ->where(['like', 'name',  $query])
            ->all();

        $items = [];
    
        foreach ($models as $model) {
            $items[] = ['name' => $model->name];
        }
        // We know we can use ContentNegotiator filter
        // this way is easier to show you here :)
        Yii::$app->response->format = Response::FORMAT_JSON;
    
        return $items;
    }
}
