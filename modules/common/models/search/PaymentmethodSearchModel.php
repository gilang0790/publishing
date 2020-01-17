<?php

namespace app\modules\common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\common\models\Paymentmethod;

/**
 * PaymentmethodSearchModel represents the model behind the search form of `app\modules\common\models\Paymentmethod`.
 */
class PaymentmethodSearchModel extends Paymentmethod
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['paymentmethodid', 'status'], 'integer'],
            [['paymentname', 'paycode'], 'safe'],
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
        $query = Paymentmethod::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['paymentmethodid' => SORT_ASC],
                'attributes' => [
                    'paymentmethodid'
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
        $query->andFilterWhere([
            'paymentmethodid' => $this->paymentmethodid,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'paymentname', $this->paymentname])
            ->andFilterWhere(['like', 'paycode', $this->paycode]);

        return $dataProvider;
    }
    
    public static function findActive() {
        return self::find()->andWhere(self::tableName() . '.status = true');
    }
}
