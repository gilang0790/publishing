<?php

namespace app\modules\admin\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\models\Menuauth;

/**
 * MenuauthSearchModel represents the model behind the search form of `app\modules\admin\models\Menuauth`.
 */
class MenuauthSearchModel extends Menuauth
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['menuauthid', 'status'], 'integer'],
            [['menuobject'], 'safe'],
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
        $query = Menuauth::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'menuauthid' => $this->menuauthid,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'menuobject', $this->menuobject]);

        return $dataProvider;
    }
    
    public static function findActive() {
        return self::find()->andWhere(self::tableName() . '.status = true');
    }
}
