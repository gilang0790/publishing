<?php

namespace app\modules\purchase\models\search;

use app\modules\purchase\models\Purchaseorderdetail;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class PurchaseorderdetailSearchModel extends Purchaseorderdetail
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'productid', 'qty', 'price', 'total'], 'integer'],
            [['id', 'productid', 'qty', 'price', 'total'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
    public function search($params)
    {
        $query = Purchaseorderdetail::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->joinWith('product');
        $query->andWhere("tr_purchaseorderdetail.head_id = $this->head_id");

        return $dataProvider;
    }
}
