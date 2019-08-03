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

    /**
     * Child class can overload this method in order to change the supportedViews list
     * if required
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->setViewPath('@app/modules/quality/views/explorer');

        // by default all QA views are supported by child class. If that's not the case, the child class must
        // overload init() and set the correct list of supported views
        $this->supportedViews = [self::VIEW_ANALYSIS, self::VIEW_SIMILARITY];

        //Yii::$app->params[\app\components\Constant::PARAM_FLUID_LAYOUT] = true;
    }

    /**
     * Main render method
     * Renders the QAA explorer page composed of a left vertical menu listing all supported
     * views, and a QA view content area, that displays QA view
     *
     * @param string $tab name of the active QA view tab
     * @param string $qaView content of the active QA view
     * @return void
     */
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

    /**
     * Render partially the Similarity view
     *
     * @param integer $threshold
     * @param array $colNameOptions
     * @param string $selectedColName
     * @param yii\db\ActiveQuery $query
     * @return void
     */
    protected function renderPartialSimilarityView($threshold = 70, $colNameOptions, $selectedColName, $query)
    {
        if (count($colNameOptions) == 1) {
            reset($colNameOptions);
            $selectedColName = key($colNameOptions);
        }
        $values= [];
        if (array_key_exists($selectedColName, $colNameOptions)) {
            $values = $this->getColumnValues($selectedColName, $query);
        } 

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
            'datasetItems' => $similarity,
            'colNameOptions' => $colNameOptions,
            'selectedColName' => $selectedColName
        ]);
    }
    /**
     * Renders the analysis view for the module.
     * The Analisys view contains a list of specific queries result that highlight some
     * business rules that may be pertinent for the child class
     * 
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

    /**
     * Displays the detailed data view related to an Analisys item
     *
     * @param array $metrics
     * @param string $id
     * @return void
     */
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

    /**
     * Queries all value for a given column namr and return the result in an array
     * where each item is a value
     *
     * @param string $colName
     * @param QueryObject $query
     * @return Array
     */
    protected function getColumnValues($colName, $query)
    {
        $queryResult = $query
            ->select($colName)
            ->distinct()
            ->asArray()
            ->all();

        // extract list of values
        return array_map(function ($item) use ($colName) {
            return $item[$colName];
        }, $queryResult);
    }

}
