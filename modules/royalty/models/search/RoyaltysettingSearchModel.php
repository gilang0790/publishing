<?php

namespace app\modules\royalty\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\royalty\models\Royaltysetting;

/**
 * RoyaltysettingSearchModel represents the model behind the search form of `app\modules\royalty\models\Royaltysetting`.
 */
class RoyaltysettingSearchModel extends Royaltysetting
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['royaltysettingid', 'period', 'createdBy', 'updatedBy'], 'integer'],
            [['fee', 'tax'], 'number'],
            [['royaltysettingid', 'addressbookid', 'productid', 'period', 'fee', 'tax', 'notes', 'status', 'createdBy', 
                'updatedBy', 'createdAt', 'updatedAt'], 'safe'],
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
        $query = Royaltysetting::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['addressbookid' => SORT_ASC],
                'attributes' => [
                    'addressbookid' => [
                        'asc' => ['ms_addressbook.fullname' => SORT_ASC],
                        'desc' => ['ms_addressbook.fullname' => SORT_DESC],
                    ],
                    'productid' => [
                        'asc' => ['ms_product.productname' => SORT_ASC],
                        'desc' => ['ms_product.productname' => SORT_DESC],
                    ], 'period', 'fee', 'tax'
                ]
            ],
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->joinWith('author');
        $query->joinWith('product');
        $query->andFilterWhere([
            'royaltysettingid' => $this->royaltysettingid,
            'period' => $this->period,
            'fee' => $this->fee,
            'tax' => $this->tax,
            'ms_royaltysetting.status' => $this->status
        ]);
        
        $query->andFilterWhere(['like', 'fullname', $this->addressbookid])
            ->andFilterWhere(['like', 'productname', $this->productid])
            ->andFilterWhere(['like', 'ms_royaltysetting.notes', $this->notes]);

        return $dataProvider;
    }
}
