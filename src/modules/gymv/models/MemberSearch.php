<?php

namespace app\modules\gymv\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Contact;
use app\models\Order;
use \app\components\SessionDateRange;
use \app\components\helpers\ConverterHelper;

/**
 * ContactSearch represents the model behind the search form of `app\models\Contact`.
 */
class MemberSearch extends Contact
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // 'birthday' is handled by addSmartDateCondition
            [['name', 'firstname', 'is_deleted', 'email', 'gender', 'birthday'], 'safe'],
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
        $query = $query === null ? QueryFactory::findQueryMembers() : $query;

        // add conditions that should always apply here
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => [
                    'name' => SORT_ASC
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
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'firstname', $this->firstname])
            ->andFilterWhere(['gender' => $this->gender])
            ->addSmartDateCondition('birthday', $this);

        return $dataProvider;
    }
}
