<?php

namespace app\modules\accounting\models\search;

use app\modules\accounting\models\Invoiceardetail;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SalesorderSearchModel represents the model behind the search form of `app\modules\inventory\models\Salesorder`.
 */
class InvoiceardetailSearchModel extends Invoiceardetail
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'productid', 'qty', 'price', 'discount', 'totaldiscount', 'vat', 'totalvat', 'total'], 'integer'],
            [['id', 'productid', 'qty', 'price', 'discount', 'totaldiscount', 'vat', 'totalvat', 'total'], 'safe'],
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
        $query = Invoiceardetail::find();

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
        $query->andWhere("tr_invoiceardetail.head_id = $this->head_id");

        return $dataProvider;
    }
}
