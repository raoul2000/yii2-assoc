<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Order;
use \app\components\helpers\DateHelper;

/**
 * OrderSearch represents the model behind the search form of `app\models\Order`.
 */
class OrderSearch extends Order
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'product_id', 'from_contact_id','to_contact_id', 'created_at', 'updated_at'], 'integer'],
            [['value'], 'number', 'min' => 0],

            [['valid_date_start', 'valid_date_end'], 'safe'] // handled by addSmartDateCondition
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $query = null)
    {
        if ($query == null) {
            $query = Order::find();
        }

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
            'product_id' => $this->product_id,
            'from_contact_id' => $this->from_contact_id,
            'to_contact_id' => $this->to_contact_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        // apply smart filter on date
        $query->addSmartDateCondition('valid_date_start', $this);
        $query->addSmartDateCondition('valid_date_end',   $this);

        return $dataProvider;
    }
}
