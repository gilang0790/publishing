<?php

namespace app\modules\inventory\models\search;

use app\components\AppHelper;
use app\modules\admin\models\Groupmenuauth;
use app\modules\inventory\models\Goodsissuereturn;
use app\modules\admin\models\Wfgroup;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * GoodsissuereturnSearchModel represents the model behind the search form of `app\modules\inventory\models\Goodsissuereturn`.
 */
class GoodsissuereturnSearchModel extends Goodsissuereturn
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
            [['girtransdate', 'goodsissueid', 'slocid', 'girtransnum', 'headernote', 'createdAt', 'updatedAt', 'dateFrom', 'dateTo'], 'safe'],
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
        $query = Goodsissuereturn::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['girtransnum' => SORT_DESC],
                'attributes' => [
                    'goodsissueid' => [
                        'asc' => ['goodsissue.gitransnum' => SORT_ASC],
                        'desc' => ['goodsissue.gitransnum' => SORT_DESC],
                    ],
                    'slocid' => [
                        'asc' => ['ms_sloc.sloccode' => SORT_ASC],
                        'desc' => ['ms_sloc.sloccode' => SORT_DESC],
                    ],
                    'goodsissueid' => [
                        'asc' => ['tr_goodsissue.gitransnum' => SORT_ASC],
                        'desc' => ['tr_goodsissue.gitransnum' => SORT_DESC],
                    ], 'girtransnum', 'girtransdate'
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
        $query->joinWith('sloc.plant.company');
        $query->joinWith('goodsissue');
        if (Groupmenuauth::getObject('company')) {
            $listCompany = Groupmenuauth::getObject('company');
        }
        if (Groupmenuauth::getObject('plant')) {
            $listPlant = Groupmenuauth::getObject('plant');
        }
        if (Wfgroup::getStatusList('listgir')) {
            $statusList = Wfgroup::getStatusList('listgir');
        }
        if ($this->slocid && $listSloc) {
            $query->andWhere("ms_sloc.slocid IN $listSloc");
        }
        $query->andWhere("ms_company.companyid IN $listCompany");
        $query->andWhere("ms_plant.plantid IN $listPlant");
        $query->andWhere("tr_goodsissuereturn.status IN $statusList");
        $query->andFilterWhere([
            'id' => $this->id,
            'tr_goodsissuereturn.status' => $this->status,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'createdBy' => $this->createdBy,
            'updatedBy' => $this->updatedBy,
        ]);

        $query->andFilterWhere(['like', 'girtransnum', $this->girtransnum])
            ->andFilterWhere(['like', 'sloccode', $this->slocid])
            ->andFilterWhere(['like', 'gitransnum', $this->goodsissueid])
            ->andFilterWhere(['>=', "DATE_FORMAT(tr_goodsissuereturn.girtransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateFrom, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['<=', "DATE_FORMAT(tr_goodsissuereturn.girtransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateTo, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['like', 'tr_goodsissuereturn.headernote', $this->headernote]);

        return $dataProvider;
    }
    
    public static function findActive() {
        return self::find()->andWhere(self::tableName() . '.status = true');
    }
}
