<?php

namespace app\modules\common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use app\modules\admin\models\Groupmenuauth;
use app\modules\common\models\Sloc;

/**
 * SlocSearchModel represents the model behind the search form of `app\modules\common\models\Sloc`.
 */
class SlocSearchModel extends Sloc
{
    public $name;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['slocid', 'status'], 'integer'],
            [['sloccode', 'plantid', 'description'], 'safe'],
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
        $listSloc = '(-1)';
        $query = Sloc::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['slocid' => SORT_ASC],
                'attributes' => [
                    'plantid' => [
                        'asc' => ['ms_plant.plantcode' => SORT_ASC],
                        'desc' => ['ms_plant.plantcode' => SORT_DESC],
                    ], 'slocid', 'sloccode', 'description'
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
        $query->joinWith('plant.company');
        if (Groupmenuauth::getObject('company')) {
            $listCompany = Groupmenuauth::getObject('company');
        }
        if (Groupmenuauth::getObject('plant')) {
            $listPlant = Groupmenuauth::getObject('plant');
        }
        if (Groupmenuauth::getObject('sloc')) {
            $listSloc = Groupmenuauth::getObject('sloc');
        }
        $query->andWhere("ms_company.companyid IN $listCompany");
        $query->andWhere("ms_plant.plantid IN $listPlant");
        $query->andWhere("ms_sloc.slocid IN $listSloc");
        $query->andFilterWhere([
            'slocid' => $this->slocid,
            'ms_sloc.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'sloccode', $this->sloccode])
            ->andFilterWhere(['like', 'plantcode', $this->plantid])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
    
    public static function dropdownList() {
        $listCompany = '(-1)';
        $listPlant = '(-1)';
        $listSloc = '(-1)';
        $query = self::find();
        $query->select(['*', 'name' => new Expression("concat(ms_plant.description, ' -- ', sloccode)")]);
        $query->innerJoinWith('plant.company');
        if (Groupmenuauth::getObject('company')) {
            $listCompany = Groupmenuauth::getObject('company');
        }
        if (Groupmenuauth::getObject('plant')) {
            $listPlant = Groupmenuauth::getObject('plant');
        }
        if (Groupmenuauth::getObject('sloc')) {
            $listSloc = Groupmenuauth::getObject('sloc');
        }
        $query->andWhere("ms_company.companyid IN $listCompany");
        $query->andWhere("ms_plant.plantid IN $listPlant");
        $query->andWhere("ms_sloc.slocid IN $listSloc");
        $query->andWhere(self::tableName() . '.status = true');
        return $query;
    }
    
    public static function findActive() {
        return self::find()->andWhere(self::tableName() . '.status = true');
    }
}
