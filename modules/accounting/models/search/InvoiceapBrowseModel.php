<?php

namespace app\modules\accounting\models\search;

use app\components\AppHelper;
use app\modules\admin\models\Groupmenuauth;
use app\modules\accounting\models\Invoiceap;
use app\modules\admin\models\Wfgroup;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class InvoiceapBrowseModel extends Invoiceap
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
            [['aptransdate', 'plantid', 'aptransnum', 'addressbookid', 'apamount', 'payamount', 'goodsreceiptid', 'headernote', 
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
        $query = Invoiceap::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['aptransnum' => SORT_DESC],
                'attributes' => [
                    'plantid' => [
                        'asc' => ['ms_plant.plantcode' => SORT_ASC],
                        'desc' => ['ms_plant.plantcode' => SORT_DESC],
                    ],
                    'addressbookid' => [
                        'asc' => ['ms_addressbook.fullname' => SORT_ASC],
                        'desc' => ['ms_addressbook.fullname' => SORT_DESC],
                    ],
                    'goodsreceiptid' => [
                        'asc' => ['tr_goodsreceipt.grtransnum' => SORT_ASC],
                        'desc' => ['tr_goodsreceipt.grtransnum' => SORT_DESC],
                    ], 'aptransnum', 'aptransdate', 'apamount', 'payamount'
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
        $query->joinWith('supplier');
        $query->joinWith('goodsreceipt');
        if (Groupmenuauth::getObject('company')) {
            $listCompany = Groupmenuauth::getObject('company');
        }
        if (Groupmenuauth::getObject('plant')) {
            $listPlant = Groupmenuauth::getObject('plant');
        }
        if (Wfgroup::getMaxStatus('listap')) {
            $maxStatus = Wfgroup::getMaxStatus('listap');
        }
        $query->andWhere("ms_company.companyid IN $listCompany");
        $query->andWhere("ms_plant.plantid IN $listPlant");
        $query->andWhere("tr_invoiceap.status = $maxStatus");
        $query->andWhere("tr_invoiceap.payamount < tr_invoiceap.apamount");
        $query->andFilterWhere([
            'id' => $this->id,
            'tr_invoiceap.status' => $this->status,
            'apamount' => $this->apamount,
            'payamount' => $this->payamount,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'createdBy' => $this->createdBy,
            'updatedBy' => $this->updatedBy,
        ]);

        $query->andFilterWhere(['like', 'aptransnum', $this->aptransnum])
            ->andFilterWhere(['like', 'ms_plant.plantcode', $this->plantid])
            ->andFilterWhere(['like', 'tr_goodsreceipt.grtransnum', $this->goodsreceiptid])
            ->andFilterWhere(['like', 'ms_addressbook.fullname', $this->addressbookid])
            ->andFilterWhere(['>=', "DATE_FORMAT(tr_invoiceap.aptransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateFrom, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['<=', "DATE_FORMAT(tr_invoiceap.aptransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateTo, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['like', 'tr_invoiceap.headernote', $this->headernote]);

        return $dataProvider;
    }
    
    public static function findActive() {
        $maxStatus = Wfgroup::getMaxStatus('listap');
        return self::find()->andWhere(self::tableName() . ".status = $maxStatus");
    }
}
