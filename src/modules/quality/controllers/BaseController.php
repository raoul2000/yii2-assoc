<?php

namespace app\modules\quality\controllers;

use Yii;
use yii\web\Controller;
use app\models\Contact;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `quality` module
 */
class BaseController extends Controller
{
    const VIEW_ANALYSIS = 'analysis';
    const VIEW_SIMILARITY = 'similarity';

    protected $pageSubHeader = '';
    protected $viewModelRoute = '';
    protected $dataColumnNames = [];

    /**
     * List of QA views supported
     *
     * @var array
     */
    protected $supportedViews = [];

    public function init()
    {
        parent::init();
        $this->setViewPath('@app/modules/quality/views/explorer');

        // by default all QA views are supported by child class. If that's not the case, the child class must
        // overload init() and set the correct list of supported views
        $this->supportedViews = [self::VIEW_ANALYSIS, self::VIEW_SIMILARITY];

        //Yii::$app->params[\app\components\Constant::PARAM_FLUID_LAYOUT] = true;
    }

    protected function renderExplorer($tab, $qaView)
    {
        if (empty($qaView)) {
            $qaView = $this->renderPartial('_no-selection');
        }
        if (Yii::$app->request->get('ajax') != null) {
            return $qaView;
        } else {
            return $this->render('index', [
                'tab' => $tab,
                'pageSubHeader' => $this->pageSubHeader,
                'supportedViews' => $this->supportedViews,
                'qaView' => $qaView
            ]);
        }
    }

    protected function renderPartialSimilarityView($values, $threshold = 70)
    {
        $index = [];
        $similarity = [];
        foreach ($values as $idx0 => $value0) {
            foreach ($values as $idx1 => $value1) {
                if ($idx1 == $idx0) {
                    continue; // don't compare with itself
                }

                // because similarity operation is symetric (a.b == b.a) we don't want to perform duplicate similarity computation
                // for this purpose, define the first and second argument using min/max operation, create and store an index out of it

                $first  = min($value0, $value1);
                $second = max($value0, $value1);

                $indexKey = $first . $second;
                if (array_key_exists($indexKey, $index)) {
                    // string pair already processed : skip
                    continue;
                }

                $matchValue = similar_text($first, $second, $percent);
                $index[$indexKey] = $matchValue;    // remember this string pair

                if ($percent > $threshold) {
                    $similarity[] = [
                        'first' => $first,
                        'second' => $second,
                        'match' => $percent
                    ];
                }
            }
        }
        // sort by best match
        usort($similarity, function ($a, $b) {
            if ($a['match'] == $b['match']) {
                return 0;
            }
            return ($a['match'] < $b['match']) ? 1 : -1;
        });

        return $this->renderPartial('_similarity', [
            'datasetItems' => $similarity
        ]);
    }
    /**
     * Renders the index view for the module
     * @return string
     */
    protected function renderPartialAnalysisView($metrics)
    {
        
        $allModels = [];
        foreach ($metrics as $id => $metric) {
            $allModels[] = [
                'id'  => $id,
                'label' => $metric['label'],
                'value' => $metric['query']->count()
            ];
        }
 
        return $this->renderPartial('_analysis', [
            'provider' => new ArrayDataProvider([
                'allModels' => $allModels,
                'pagination' => [
                    'pageSize' => 10,
                ],
            ])
        ]);
    }

    protected function implActionViewData($metrics, $id)
    {
        
        if (!array_key_exists($id, $metrics)) {
            throw new NotFoundHttpException('Data set id not found');
        }
        $metric = $metrics[$id];

        return $this->render('view-data', [
            'id' => $id,
            'pageSubHeader' => $this->pageSubHeader,
            'viewModelRoute' => $this->viewModelRoute,
            'dataColumnNames' => $this->dataColumnNames,
            'dataProvider' => new ActiveDataProvider([
                'query' => $metric['query'],
                'pagination' => [
                    'pageSize' => 50,
                ],
            ]),
            'label' => $metric['label']
        ]);
    }
}
