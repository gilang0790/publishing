<?php

namespace app\modules\inventory\models\search;

use app\components\AppHelper;
use app\modules\admin\models\Groupmenuauth;
use app\modules\inventory\models\Stockopname;
use app\modules\admin\models\Wfgroup;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * StockopnameSearchModel represents the model behind the search form of `app\modules\inventory\models\Stockopname`.
 */
class StockopnameSearchModel extends Stockopname
{
    public $dateFrom;
    public $dateTo;
    public $fileUpload;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'createdBy', 'updatedBy'], 'integer'],
            [['bstransdate', 'slocid', 'bstransnum', 'headernote', 'createdAt', 'updatedAt', 'dateFrom', 'dateTo'], 'safe'],
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
        $statusList = '(-1)';
        $query = Stockopname::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['bstransnum' => SORT_DESC],
                'attributes' => [
                    'companyid' => [
                        'asc' => ['ms_sloc.sloccode' => SORT_ASC],
                        'desc' => ['ms_sloc.sloccode' => SORT_DESC],
                    ],
                    'slocid' => [
                        'asc' => ['ms_sloc.sloccode' => SORT_ASC],
                        'desc' => ['ms_sloc.sloccode' => SORT_DESC],
                    ], 'bstransnum', 'bstransdate'
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
        if (Groupmenuauth::getObject('company')) {
            $listCompany = Groupmenuauth::getObject('company');
        }
        if (Groupmenuauth::getObject('plant')) {
            $listPlant = Groupmenuauth::getObject('plant');
        }
        if (Groupmenuauth::getObject('sloc')) {
            $listSloc = Groupmenuauth::getObject('sloc');
        }
        if (Wfgroup::getStatusList('listbs')) {
            $statusList = Wfgroup::getStatusList('listbs');
        }
        $query->andWhere("ms_company.companyid IN $listCompany");
        $query->andWhere("ms_plant.plantid IN $listPlant");
        $query->andWhere("ms_sloc.slocid IN $listSloc");
        $query->andWhere("tr_stockopname.status IN $statusList");
        $query->andFilterWhere([
            'id' => $this->id,
            'tr_stockopname.status' => $this->status,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'createdBy' => $this->createdBy,
            'updatedBy' => $this->updatedBy,
        ]);

        $query->andFilterWhere(['like', 'bstransnum', $this->bstransnum])
            ->andFilterWhere(['like', 'sloccode', $this->slocid])
            ->andFilterWhere(['>=', "DATE_FORMAT(tr_stockopname.bstransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateFrom, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['<=', "DATE_FORMAT(tr_stockopname.bstransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateTo, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['like', 'headernote', $this->headernote]);

        return $dataProvider;
    }
    
    public static function findActive() {
        return self::find()->andWhere(self::tableName() . '.status = true');
    }
}
