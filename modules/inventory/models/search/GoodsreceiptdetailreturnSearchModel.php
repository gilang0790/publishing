<?php

namespace app\modules\inventory\models\search;

use app\modules\inventory\models\Goodsreceiptdetailreturn;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * GoodsissueSearchModel represents the model behind the search form of `app\modules\inventory\models\Goodsreceiptdetailreturn`.
 */
class GoodsreceiptdetailreturnSearchModel extends Goodsreceiptdetailreturn
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'productid', 'qty', 'storagebinid'], 'integer'],
            [['id', 'productid', 'qty', 'storagebinid'], 'safe'],
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
        $query = Goodsreceiptdetailreturn::find();

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
        $query->joinWith('storagebin');
        $query->andWhere("tr_goodsreceiptdetailreturn.head_id = $this->head_id");

        return $dataProvider;
    }
}
