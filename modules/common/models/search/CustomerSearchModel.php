<?php

namespace app\modules\common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\common\models\Customer;

/**
 * CustomerSearchModel represents the model behind the search form of `app\modules\common\models\Customer`.
 */
class CustomerSearchModel extends Customer
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['addressbookid', 'iscustomer', 'isemployee', 'isvendor', 'ishospital', 'discount', 'status'], 'integer'],
            [['fullname', 'pic', 'publishercode', 'bankname', 'bankaccountno', 'cityid', 'address', 'email', 'phoneno'], 'safe'],
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
        $query = Customer::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['fullname' => SORT_ASC],
                'attributes' => [
                    'cityid' => [
                        'asc' => ['ms_city.cityname' => SORT_ASC],
                        'desc' => ['ms_city.cityname' => SORT_DESC],
                    ], 'fullname', 'pic', 'publishercode', 'bankname', 'bankaccountno', 'cityid', 'address', 'email', 'phoneno'
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
        $query->joinWith('city');
        $query->andWhere('iscustomer = 1');
        $query->andFilterWhere([
            'addressbookid' => $this->addressbookid,
            'discount' => $this->discount,
            'ms_addressbook.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'fullname', $this->fullname])
            ->andFilterWhere(['like', 'pic', $this->pic])
            ->andFilterWhere(['like', 'publishercode', $this->publishercode])
            ->andFilterWhere(['like', 'bankname', $this->bankname])
            ->andFilterWhere(['like', 'bankaccountno', $this->bankaccountno])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phoneno', $this->phoneno])
            ->andFilterWhere(['like', 'cityname', $this->cityid]);

        return $dataProvider;
    }
    
    public static function findActive() {
        return self::find()
            ->andWhere(self::tableName() . '.iscustomer = true')
            ->andWhere(self::tableName() . '.status = true');
    }
}
