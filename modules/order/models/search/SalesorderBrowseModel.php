<?php

namespace app\modules\order\models\search;

use app\components\AppHelper;
use app\modules\admin\models\Groupmenuauth;
use app\modules\order\models\Salesorder;
use app\modules\admin\models\Wfgroup;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class SalesorderBrowseModel extends Salesorder
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
            [['sotransdate', 'plantid', 'sotransnum', 'addressbookid', 'pocustomer', 'salestype', 'address', 'headernote', 
                'createdAt', 'updatedAt', 'dateFrom', 'dateTo'], 'safe'],
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
        $maxStatus = -1;
        $query = Salesorder::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['sotransnum' => SORT_ASC],
                'attributes' => [
                    'plantid' => [
                        'asc' => ['ms_plant.plantcode' => SORT_ASC],
                        'desc' => ['ms_plant.plantcode' => SORT_DESC],
                    ],
                    'addressbookid' => [
                        'asc' => ['ms_addressbook.fullname' => SORT_ASC],
                        'desc' => ['ms_addressbook.fullname' => SORT_DESC],
                    ],
                    'status' => [
                        'asc' => ['tr_salesorder.status' => SORT_ASC],
                        'desc' => ['tr_salesorder.status' => SORT_DESC],
                    ], 'sotransnum', 'sotransdate', 'grandtotal', 'salestype'
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
        if (Groupmenuauth::getObject('company')) {
            $listCompany = Groupmenuauth::getObject('company');
        }
        if (Groupmenuauth::getObject('plant')) {
            $listPlant = Groupmenuauth::getObject('plant');
        }
        if (Wfgroup::getMaxStatus('listso')) {
            $maxStatus = Wfgroup::getMaxStatus('listso');
        }
        $query->andWhere("ms_company.companyid IN $listCompany");
        $query->andWhere("ms_plant.plantid IN $listPlant");
        $query->andWhere("tr_salesorder.status = $maxStatus");
        $query->andWhere("tr_salesorder.isgenerated IS NULL");
        $query->andFilterWhere([
            'id' => $this->id,
            'tr_salesorder.status' => $this->status,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'createdBy' => $this->createdBy,
            'updatedBy' => $this->updatedBy,
        ]);

        $query->andFilterWhere(['like', 'sotransnum', $this->sotransnum])
            ->andFilterWhere(['like', 'pocustomer', $this->pocustomer])
            ->andFilterWhere(['like', 'salestype', $this->salestype])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'ms_plant.plantcode', $this->plantid])
            ->andFilterWhere(['like', 'ms_addressbook.fullname', $this->addressbookid])
            ->andFilterWhere(['>=', "DATE_FORMAT(tr_salesorder.sotransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateFrom, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['<=', "DATE_FORMAT(tr_salesorder.sotransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateTo, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['like', 'headernote', $this->headernote]);

        return $dataProvider;
    }
    
    public function findActive() {
        $listCompany = '(-1)';
        $listPlant = '(-1)';
        $maxStatus = -1;
        $advanceSoList = '(-1)';
        $query = Salesorder::find();

        // grid filtering conditions
        $query->joinWith('plant.company');
        $query->joinWith('customer');
        if (Groupmenuauth::getObject('company')) {
            $listCompany = Groupmenuauth::getObject('company');
        }
        if (Groupmenuauth::getObject('plant')) {
            $listPlant = Groupmenuauth::getObject('plant');
        }
        if (Wfgroup::getMaxStatus('listso')) {
            $maxStatus = Wfgroup::getMaxStatus('listso');
        }
        if (Salesorder::getAdvanceSoList()) {
            $advanceSoList = Salesorder::getAdvanceSoList();
        }
        
        $query->andWhere("ms_company.companyid IN $listCompany");
        $query->andWhere("ms_plant.plantid IN $listPlant");
        $query->andWhere("tr_salesorder.status = $maxStatus");
        $query->andWhere("tr_salesorder.id NOT IN $advanceSoList");
        
        return $query->all();
    }
}
