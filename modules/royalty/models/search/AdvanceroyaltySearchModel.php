<?php

namespace app\modules\royalty\models\search;

use app\components\AppHelper;
use app\modules\admin\models\Groupmenuauth;
use app\modules\royalty\models\Advanceroyalty;
use app\modules\admin\models\Wfgroup;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class AdvanceroyaltySearchModel extends Advanceroyalty
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
            [['umrtransdate', 'plantid', 'umrtransnum', 'productid', 'amount', 'addressbookid', 'headernote', 'isUsed',
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
        $query = Advanceroyalty::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['umrtransdate' => SORT_ASC],
                'attributes' => [
                    'plantid' => [
                        'asc' => ['ms_plant.plantcode' => SORT_ASC],
                        'desc' => ['ms_plant.plantcode' => SORT_DESC],
                    ],
                    'addressbookid' => [
                        'asc' => ['ms_addressbook.fullname' => SORT_ASC],
                        'desc' => ['ms_addressbook.fullname' => SORT_DESC],
                    ],
                    'productid' => [
                        'asc' => ['ms_product.productname' => SORT_ASC],
                        'desc' => ['ms_product.productname' => SORT_DESC],
                    ], 'umrtransdate', 'umrtransnum', 'amount'
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
        $query->joinWith('author');
        $query->joinWith('plant.company');
        $query->joinWith('product');
        if (Groupmenuauth::getObject('company')) {
            $listCompany = Groupmenuauth::getObject('company');
        }
        if (Groupmenuauth::getObject('plant')) {
            $listPlant = Groupmenuauth::getObject('plant');
        }
        if (Wfgroup::getStatusList('listum')) {
            $statusList = Wfgroup::getStatusList('listumr');
        }
        $query->andWhere("ms_company.companyid IN $listCompany");
        $query->andWhere("ms_plant.plantid IN $listPlant");
        $query->andWhere("tr_advanceroyalty.status IN $statusList");
        $query->andFilterWhere([
            'id' => $this->id,
            'tr_advanceroyalty.status' => $this->status,
            'amount' => $this->amount,
            'isUsed' => $this->isUsed,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'createdBy' => $this->createdBy,
            'updatedBy' => $this->updatedBy,
        ]);

        $query->andFilterWhere(['like', 'umrtransnum', $this->umrtransnum])
            ->andFilterWhere(['like', 'productname', $this->productid])
            ->andFilterWhere(['like', 'ms_plant.plantcode', $this->plantid])
            ->andFilterWhere(['like', 'ms_addressbook.fullname', $this->addressbookid])
            ->andFilterWhere(['>=', "DATE_FORMAT(tr_advanceroyalty.umrtransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateFrom, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['<=', "DATE_FORMAT(tr_advanceroyalty.umrtransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateTo, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['like', 'headernote', $this->headernote]);

        return $dataProvider;
    }
}
