<?php

namespace app\modules\gymv\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Product;

/**
 * ProductSearch represents the model behind the search form of `app\models\Product`.
 */
class ProductCourseSearch extends \app\models\ProductSearch
{
    public $order_count;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        //$rules[] = 
        return $rules;

        return [
            [['id', 'created_at', 'updated_at', 'category_id'], 'integer'],
            [['name', 'short_description'], 'safe'],
            [['value'], 'number', 'validOrdersCount'],
            
            [['valid_date_start', 'valid_date_end'], 'safe'] // handled by addSmartDateCondition
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ProductCourse::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => [
                    'updated_at' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'value' => $this->value,
            'category_id' => $this->category_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'short_description', $this->short_description]);

        // apply smart filter on date
        $query->addSmartDateCondition('valid_date_start', $this);
        $query->addSmartDateCondition('valid_date_end',   $this);        

        return $dataProvider;
    }
}
