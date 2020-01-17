<?php

namespace app\modules\accounting\models\search;

use app\components\AppHelper;
use app\modules\admin\models\Groupmenuauth;
use app\modules\accounting\models\Advancepayment;
use app\modules\admin\models\Wfgroup;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class AdvancepaymentSearchModel extends Advancepayment
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
            [['umtransdate', 'plantid', 'umtransnum', 'amount', 'salesorderid', 'headernote', 'isUsed',
                'bankname', 'bankaccountno', 'receiptno', 'createdAt', 'updatedAt', 'dateFrom', 'dateTo'], 'safe'],
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
        $statusList = '(-1)';
        $query = Advancepayment::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['umtransnum' => SORT_DESC],
                'attributes' => [
                    'plantid' => [
                        'asc' => ['ms_plant.plantcode' => SORT_ASC],
                        'desc' => ['ms_plant.plantcode' => SORT_DESC],
                    ],
                    'salesorderid' => [
                        'asc' => ['tr_salesorder.sotransnum' => SORT_ASC],
                        'desc' => ['tr_salesorder.sotransnum' => SORT_DESC],
                    ], 'umtransnum', 'umtransdate', 'amount'
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
        $query->joinWith('salesorder');
        $query->joinWith('plant.company');
        if (Groupmenuauth::getObject('company')) {
            $listCompany = Groupmenuauth::getObject('company');
        }
        if (Groupmenuauth::getObject('plant')) {
            $listPlant = Groupmenuauth::getObject('plant');
        }
        if (Wfgroup::getStatusList('listum')) {
            $statusList = Wfgroup::getStatusList('listum');
        }
        $query->andWhere("ms_company.companyid IN $listCompany");
        $query->andWhere("ms_plant.plantid IN $listPlant");
        $query->andWhere("tr_advancepayment.status IN $statusList");
        $query->andFilterWhere([
            'id' => $this->id,
            'tr_advancepayment.status' => $this->status,
            'amount' => $this->amount,
            'isUsed' => $this->isUsed,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'createdBy' => $this->createdBy,
            'updatedBy' => $this->updatedBy,
        ]);

        $query->andFilterWhere(['like', 'umtransnum', $this->umtransnum])
            ->andFilterWhere(['like', 'ms_plant.plantcode', $this->plantid])
            ->andFilterWhere(['like', 'tr_salesorder.sotransnum', $this->salesorderid])
            ->andFilterWhere(['>=', "DATE_FORMAT(tr_advancepayment.umtransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateFrom, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['<=', "DATE_FORMAT(tr_advancepayment.umtransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateTo, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['like', 'headernote', $this->headernote]);

        return $dataProvider;
    }
    
    public static function findActive() {
        return self::find()->andWhere(self::tableName() . '.status = true');
    }
}
