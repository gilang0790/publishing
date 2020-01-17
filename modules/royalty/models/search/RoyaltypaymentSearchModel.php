<?php

namespace app\modules\royalty\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\components\AppHelper;
use app\modules\admin\models\Groupmenuauth;
use app\modules\royalty\models\Royaltypayment;
use app\modules\admin\models\Wfgroup;

/**
 * RoyaltypaymentSearchModel represents the model behind the search form of `app\modules\royalty\models\Royaltypayment`.
 */
class RoyaltypaymentSearchModel extends Royaltypayment
{
    public $dateFrom;
    public $dateTo;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'lockBy', 'createdBy', 'updatedBy'], 'integer'],
            [['rptransdate', 'rptransnum', 'plantid', 'invoiceroyaltyid', 'advanceroyaltyid', 'bankname', 'bankaccountno', 
                'receiptno', 'headernote', 'lockDateUntil', 'createdAt', 'updatedAt', 'dateFrom', 'dateTo'], 'safe'],
            [['invoiceamount', 'paidamount', 'advanceamount', 'payamount'], 'number'],
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
        $query = Royaltypayment::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['rptransnum' => SORT_DESC],
                'attributes' => [
                    'plantid' => [
                        'asc' => ['ms_plant.plantcode' => SORT_ASC],
                        'desc' => ['ms_plant.plantcode' => SORT_DESC],
                    ],
                    'invoiceroyaltyid' => [
                        'asc' => ['tr_invoiceroyalty.transnum' => SORT_ASC],
                        'desc' => ['tr_invoiceroyalty.transnum' => SORT_DESC],
                    ], 'rptransnum', 'rptransdate', 'invoiceamount', 'paidamount', 'advanceamount', 'payamount'
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
        $query->joinWith('advanceroyalty');
        $query->joinWith('invoiceroyalty');
        if (Groupmenuauth::getObject('company')) {
            $listCompany = Groupmenuauth::getObject('company');
        }
        if (Groupmenuauth::getObject('plant')) {
            $listPlant = Groupmenuauth::getObject('plant');
        }
        if (Wfgroup::getStatusList('listumr')) {
            $statusList = Wfgroup::getStatusList('listumr');
        }
        $query->andWhere("ms_company.companyid IN $listCompany");
        $query->andWhere("ms_plant.plantid IN $listPlant");
        $query->andWhere("tr_royaltypayment.status IN $statusList");
        $query->andFilterWhere([
            'id' => $this->id,
            'invoiceamount' => $this->invoiceamount,
            'paidamount' => $this->paidamount,
            'advanceamount' => $this->advanceamount,
            'payamount' => $this->payamount,
            'tr_royaltypayment.status' => $this->status,
            'lockDateUntil' => $this->lockDateUntil,
            'lockBy' => $this->lockBy,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'createdBy' => $this->createdBy,
            'updatedBy' => $this->updatedBy,
        ]);

        $query->andFilterWhere(['like', 'rptransnum', $this->rptransnum])
            ->andFilterWhere(['like', 'tr_advanceroyalty.umrtransnum', $this->advanceroyaltyid])
            ->andFilterWhere(['like', 'tr_invoiceroyalty.transnum', $this->invoiceroyaltyid])
            ->andFilterWhere(['like', 'ms_plant.plantcode', $this->plantid])
            ->andFilterWhere(['like', 'bankname', $this->bankname])
            ->andFilterWhere(['like', 'bankaccountno', $this->bankaccountno])
            ->andFilterWhere(['like', 'receiptno', $this->receiptno])
            ->andFilterWhere(['like', 'headernote', $this->headernote])
            ->andFilterWhere(['>=', "DATE_FORMAT(tr_royaltypayment.rptransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateFrom, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['<=', "DATE_FORMAT(tr_royaltypayment.rptransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateTo, 'd-m-Y', 'Y-m-d')]);

        return $dataProvider;
    }
    
    public static function findActive() {
        return self::find()->andWhere(self::tableName() . '.status = true');
    }
}
