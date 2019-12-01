<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Transaction;
use app\components\Constant;

/**
 * TransactionSearch represents the model behind the search form of `app\models\Transaction`.
 */
class TransactionPerAccountSearch extends Transaction
{
    public $debit;
    public $credit;
    public $account_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'from_account_id', 'to_account_id', 'created_at', 'category_id', 'updated_at', 'transaction_pack_id', 'debit', 'credit', 'account_id'], 'integer'],
            [['is_verified'], 'boolean'],
            [['value'], 'number'],
            [['description', 'code', 'type', 'reference_date'], 'safe'],
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
    public function search($params, $bankAccount)
    {

        $query = $bankAccount->getTransactions();
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        if (!empty($this->credit)) {
            $query->andWhere([
                'to_account_id' => $bankAccount->id,
                'value' => $this->credit
            ]);
        }
        if (!empty($this->debit)) {
            $query->andWhere([
                'from_account_id' => $bankAccount->id,
                'value' => $this->debit
            ]);
        }
        
        // only transactions to or from the provided account_id
        if (!empty($this->account_id)) {
            $query->andWhere([
                'or' ,  'from_account_id=' .  $this->account_id , 'to_account_id=' .$this->account_id
            ]);
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'category_id' => $this->category_id,
        ]);
        // grid filtering conditions
        /*
        $query->andFilterWhere([
            'id' => $this->id,
            'from_account_id' => $this->from_account_id,
            'to_account_id' => $this->to_account_id,
            'value' => $this->value,
            'is_verified' => $this->is_verified,
            'code' => $this->code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'transaction_pack_id' => $this->transaction_pack_id,
            'type' => $this->type
        ]);*/

        $query->andFilterWhere(['like', 'description', $this->description]);
        $query->addSmartDateCondition('reference_date', $this);

        return $dataProvider;
    }
}
