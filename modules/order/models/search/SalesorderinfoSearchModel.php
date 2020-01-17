<?php

namespace app\modules\order\models\search;

use app\modules\admin\models\Groupmenuauth;
use app\modules\order\models\Salesorderinfo;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SalesorderSearchModel represents the model behind the search form of `app\modules\inventory\models\Salesorder`.
 */
class SalesorderinfoSearchModel extends Salesorderinfo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'createdBy', 'updatedBy'], 'integer'],
            [['sotransdate', 'plantid', 'salesorderid', 'createdAt', 'updatedAt', 'dateFrom', 'dateTo'], 'safe'],
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
        $query = Salesorderinfo::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_ASC],
                'attributes' => [
                    'plantid' => [
                        'asc' => ['ms_plant.plantcode' => SORT_ASC],
                        'desc' => ['ms_plant.plantcode' => SORT_DESC],
                    ],
                    'salesorderid' => [
                        'asc' => ['tr_salesorder.sotransnum' => SORT_ASC],
                        'desc' => ['tr_salesorder.sotransnum' => SORT_DESC],
                    ],'id'
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
        $query->joinWith('salesorder');
        if (Groupmenuauth::getObject('company')) {
            $listCompany = Groupmenuauth::getObject('company');
        }
        if (Groupmenuauth::getObject('plant')) {
            $listPlant = Groupmenuauth::getObject('plant');
        }
        $query->andWhere("ms_company.companyid IN $listCompany");
        $query->andWhere("ms_plant.plantid IN $listPlant");
        $query->andFilterWhere([
            'id' => $this->id,
            'tr_salesorderinfo.status' => $this->status,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'createdBy' => $this->createdBy,
            'updatedBy' => $this->updatedBy,
        ]);

        $query->andFilterWhere(['like', 'ms_plant.plantcode', $this->plantid])
            ->andFilterWhere(['like', 'tr_salesorder.sotransnum', $this->salesorderid]);

        return $dataProvider;
    }
    
    public static function findActive() {
        return self::find()->andWhere(self::tableName() . '.status = true');
    }
}
