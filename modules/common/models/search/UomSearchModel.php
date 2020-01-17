<?php

namespace app\modules\common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\common\models\Unitofmeasure;

/**
 * UomSearchModel represents the model behind the search form of `app\modules\common\models\Unitofmeasure`.
 */
class UomSearchModel extends Unitofmeasure
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['unitofmeasureid', 'status'], 'integer'],
            [['uomcode', 'description'], 'safe'],
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
        $query = Unitofmeasure::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['unitofmeasureid' => SORT_ASC],
                'attributes' => [
                    'unitofmeasureid'
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
            'unitofmeasureid' => $this->unitofmeasureid,
            'ms_unitofmeasure.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'uomcode', $this->uomcode])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
    
    public static function findActive() {
        return self::find()->andWhere(self::tableName() . '.status = true');
    }
}
