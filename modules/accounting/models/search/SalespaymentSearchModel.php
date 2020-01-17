<?php

namespace app\modules\accounting\models\search;

use app\components\AppHelper;
use app\modules\admin\models\Groupmenuauth;
use app\modules\accounting\models\Salespayment;
use app\modules\admin\models\Wfgroup;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class SalespaymentSearchModel extends Salespayment
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
            [['sptransdate', 'plantid', 'sptransnum', 'addressbookid', 'paidamount', 'advanceamount', 'aramount', 'payamount', 'invoicearid', 
                'advancepaymentid', 'headernote', 'bankname', 'bankaccountno', 'receiptno', 'createdAt', 'updatedAt', 'dateFrom', 'dateTo'], 'safe'],
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
        $query = Salespayment::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['sptransnum' => SORT_DESC],
                'attributes' => [
                    'plantid' => [
                        'asc' => ['ms_plant.plantcode' => SORT_ASC],
                        'desc' => ['ms_plant.plantcode' => SORT_DESC],
                    ],
                    'invoicearid' => [
                        'asc' => ['tr_invoicear.artransnum' => SORT_ASC],
                        'desc' => ['tr_invoicear.artransnum' => SORT_DESC],
                    ], 'sptransnum', 'sptransdate', 'aramount', 'paidamount', 'advanceamount', 'payamount'
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
        $query->joinWith('customer');
        $query->joinWith('invoicear');
        if (Groupmenuauth::getObject('company')) {
            $listCompany = Groupmenuauth::getObject('company');
        }
        if (Groupmenuauth::getObject('plant')) {
            $listPlant = Groupmenuauth::getObject('plant');
        }
        if (Wfgroup::getStatusList('listsp')) {
            $statusList = Wfgroup::getStatusList('listsp');
        }
        $query->andWhere("ms_company.companyid IN $listCompany");
        $query->andWhere("ms_plant.plantid IN $listPlant");
        $query->andWhere("tr_salespayment.status IN $statusList");
        $query->andFilterWhere([
            'id' => $this->id,
            'tr_salespayment.status' => $this->status,
            'advanceamount' => $this->advanceamount,
            'aramount' => $this->aramount,
            'paidamount' => $this->paidamount,
            'payamount' => $this->payamount,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'createdBy' => $this->createdBy,
            'updatedBy' => $this->updatedBy,
        ]);

        $query->andFilterWhere(['like', 'sptransnum', $this->sptransnum])
            ->andFilterWhere(['like', 'ms_plant.plantcode', $this->plantid])
            ->andFilterWhere(['like', 'ms_addressbook.fullname', $this->addressbookid])
            ->andFilterWhere(['like', 'tr_invoicear.artransnum', $this->invoicearid])
            ->andFilterWhere(['like', 'tr_advancepayment.umtransnum', $this->advancepaymentid])
            ->andFilterWhere(['>=', "DATE_FORMAT(tr_salespayment.sptransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateFrom, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['<=', "DATE_FORMAT(tr_salespayment.sptransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateTo, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['like', 'headernote', $this->headernote]);

        return $dataProvider;
    }
    
    public static function findActive() {
        return self::find()->andWhere(self::tableName() . '.status = true');
    }
}
