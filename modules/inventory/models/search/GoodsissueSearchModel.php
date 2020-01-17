<?php

namespace app\modules\inventory\models\search;

use app\components\AppHelper;
use app\modules\admin\models\Groupmenuauth;
use app\modules\inventory\models\Goodsissue;
use app\modules\admin\models\Wfgroup;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * GoodsissueSearchModel represents the model behind the search form of `app\modules\inventory\models\Goodsissue`.
 */
class GoodsissueSearchModel extends Goodsissue
{
    public $dateFrom;
    public $dateTo;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'createdBy', 'updatedBy'], 'integer'],
            [['gitransdate', 'salesorderid', 'slocid', 'gitransnum', 'headernote', 'createdAt', 'updatedAt', 'dateFrom', 'dateTo'], 'safe'],
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
        $listSloc = Groupmenuauth::getObject('sloc');
        $statusList = '(-1)';
        $query = Goodsissue::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['gitransnum' => SORT_DESC],
                'attributes' => [
                    'salesorderid' => [
                        'asc' => ['tr_salesorder.sotransnum' => SORT_ASC],
                        'desc' => ['tr_salesorder.sotransnum' => SORT_DESC],
                    ],
                    'slocid' => [
                        'asc' => ['ms_sloc.sloccode' => SORT_ASC],
                        'desc' => ['ms_sloc.sloccode' => SORT_DESC],
                    ], 'gitransnum', 'gitransdate'
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
        $query->joinWith('salesorder.plant.company');
        $query->joinWith('sloc');
        if (Groupmenuauth::getObject('company')) {
            $listCompany = Groupmenuauth::getObject('company');
        }
        if (Groupmenuauth::getObject('plant')) {
            $listPlant = Groupmenuauth::getObject('plant');
        }
        if (Wfgroup::getStatusList('listgi')) {
            $statusList = Wfgroup::getStatusList('listgi');
        }
        if ($this->slocid && $listSloc) {
            $query->andWhere("ms_sloc.slocid IN $listSloc");
        }
        $query->andWhere("ms_company.companyid IN $listCompany");
        $query->andWhere("ms_plant.plantid IN $listPlant");
        $query->andWhere("tr_goodsissue.status IN $statusList");
        $query->andFilterWhere([
            'id' => $this->id,
            'tr_goodsissue.status' => $this->status,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'createdBy' => $this->createdBy,
            'updatedBy' => $this->updatedBy,
        ]);

        $query->andFilterWhere(['like', 'gitransnum', $this->gitransnum])
            ->andFilterWhere(['like', 'sloccode', $this->slocid])
            ->andFilterWhere(['like', 'sotransnum', $this->salesorderid])
            ->andFilterWhere(['>=', "DATE_FORMAT(tr_goodsissue.gitransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateFrom, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['<=', "DATE_FORMAT(tr_goodsissue.gitransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateTo, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['like', 'tr_goodsissue.headernote', $this->headernote]);

        return $dataProvider;
    }
    
    public static function findActive() {
        return self::find()->andWhere(self::tableName() . '.status = true');
    }
}
