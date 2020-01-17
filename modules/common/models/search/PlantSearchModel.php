<?php

namespace app\modules\common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use app\modules\common\models\Plant;
use app\modules\admin\models\Groupmenuauth;

/**
 * PlantSearchModel represents the model behind the search form of `app\modules\common\models\Plant`.
 */
class PlantSearchModel extends Plant
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['plantid', 'status'], 'integer'],
            [['plantcode', 'companyid', 'description'], 'safe'],
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
        $listCompany = '(-1)';
        $listPlant = '(-1)';
        $query = Plant::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['plantid' => SORT_ASC],
                'attributes' => [
                    'companyid' => [
                        'asc' => ['ms_company.companyname' => SORT_ASC],
                        'desc' => ['ms_company.companyname' => SORT_DESC],
                    ], 'plantid', 'plantcode', 'companyid', 'description'
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
        $query->innerJoinWith('company');
        if (Groupmenuauth::getObject('company')) {
            $listCompany = Groupmenuauth::getObject('company');
        }
        if (Groupmenuauth::getObject('plant')) {
            $listPlant = Groupmenuauth::getObject('plant');
        }
        $query->andWhere("ms_company.companyid IN $listCompany");
        $query->andWhere("ms_plant.plantid IN $listPlant");
        $query->andFilterWhere([
            'plantid' => $this->plantid,
            'ms_plant.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'plantcode', $this->plantcode])
            ->andFilterWhere(['like', 'companyname', $this->companyid])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
    
    public static function dropdownList() {
        $listCompany = '(-1)';
        $listPlant = '(-1)';
        $query = self::find();
        $query->select(['*', 'name' => new Expression("concat(ms_company.companycode, ' -- ', plantcode)")]);
        $query->innerJoinWith('company');
        if (Groupmenuauth::getObject('company')) {
            $listCompany = Groupmenuauth::getObject('company');
        }
        if (Groupmenuauth::getObject('plant')) {
            $listPlant = Groupmenuauth::getObject('plant');
        }
        $query->andWhere("ms_company.companyid IN $listCompany");
        $query->andWhere("ms_plant.plantid IN $listPlant");
        $query->andWhere(self::tableName() . '.status = true');
        return $query;
    }
    
    public static function findActive() {
        return self::find()->andWhere(self::tableName() . '.status = true');
    }
}
