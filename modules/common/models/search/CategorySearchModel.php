<?php

namespace app\modules\common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\common\models\Category;

/**
 * CategorySearchModel represents the model behind the search form of `app\modules\common\models\Category`.
 */
class CategorySearchModel extends Category
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['categoryid', 'status'], 'integer'],
            [['categoryname', 'categorycode'], 'safe'],
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
        $query = Category::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['categoryname' => SORT_ASC],
                'attributes' => [
                    'categoryid', 'categoryname', 'categorycode'
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
            'categoryid' => $this->categoryid,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'categoryname', $this->categoryname])
            ->andFilterWhere(['like', 'categorycode', $this->categorycode]);

        return $dataProvider;
    }
    
    public static function findActive() {
        return self::find()->andWhere(self::tableName() . '.status = true');
    }
}
