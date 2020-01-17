<?php

namespace app\modules\inventory\models\search;

use app\components\AppHelper;
use app\modules\admin\models\Groupmenuauth;
use app\modules\inventory\models\Goodsreceipt;
use app\modules\inventory\models\Goodsreceiptdetail;
use app\modules\admin\models\Wfgroup;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * GoodsreceiptSearchModel represents the model behind the search form of `app\modules\inventory\models\Goodsreceipt`.
 */
class GoodsreceiptBrowseModel extends Goodsreceipt
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
            [['grtransdate', 'purchaseorderid', 'slocid', 'grtransnum', 'headernote', 'createdAt', 'updatedAt', 'dateFrom', 'dateTo'], 'safe'],
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
        $maxStatus = -1;
        $listOustanding = '(-1)';
        $query = Goodsreceipt::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['grtransnum' => SORT_DESC],
                'attributes' => [
                    'purchaseorderid' => [
                        'asc' => ['purchaseorder.potransnum' => SORT_ASC],
                        'desc' => ['purchaseorder.potransnum' => SORT_DESC],
                    ],
                    'slocid' => [
                        'asc' => ['ms_sloc.sloccode' => SORT_ASC],
                        'desc' => ['ms_sloc.sloccode' => SORT_DESC],
                    ], 'grtransnum', 'grtransdate'
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
        $query->joinWith('sloc');
        $query->joinWith('purchaseorder.plant.company');
        if (Groupmenuauth::getObject('company')) {
            $listCompany = Groupmenuauth::getObject('company');
        }
        if (Groupmenuauth::getObject('plant')) {
            $listPlant = Groupmenuauth::getObject('plant');
        }
        if (Groupmenuauth::getObject('sloc')) {
            $listSloc = Groupmenuauth::getObject('sloc');
        }
        if (Wfgroup::getMaxStatus('listgr')) {
            $maxStatus = Wfgroup::getMaxStatus('listgr');
        }
        if (Goodsreceiptdetail::getOutstanding()) {
            $listOustanding = Goodsreceiptdetail::getOutstanding();
        }
        $query->andWhere("ms_company.companyid IN $listCompany");
        $query->andWhere("ms_plant.plantid IN $listPlant");
        $query->andWhere("ms_sloc.slocid IN $listSloc");
        $query->andWhere("tr_goodsreceipt.status = $maxStatus");
        $query->andWhere("tr_goodsreceipt.id IN $listOustanding");
        $query->andFilterWhere([
            'id' => $this->id,
            'tr_goodsreceipt.status' => $this->status,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'createdBy' => $this->createdBy,
            'updatedBy' => $this->updatedBy,
        ]);

        $query->andFilterWhere(['like', 'grtransnum', $this->grtransnum])
            ->andFilterWhere(['like', 'sloccode', $this->slocid])
            ->andFilterWhere(['like', 'potransnum', $this->purchaseorderid])
            ->andFilterWhere(['>=', "DATE_FORMAT(tr_goodsreceipt.grtransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateFrom, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['<=', "DATE_FORMAT(tr_goodsreceipt.grtransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateTo, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['like', 'tr_goodsreceipt.headernote', $this->headernote]);

        return $dataProvider;
    }
    
    public function searchReturn($params)
    {
        $listCompany = '(-1)';
        $listPlant = '(-1)';
        $listSloc = Groupmenuauth::getObject('sloc');
        $listInvoice = '(-1)';
        $maxStatus = -1;
        $query = Goodsreceipt::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['grtransnum' => SORT_DESC],
                'attributes' => [
                    'purchaseorderid' => [
                        'asc' => ['purchaseorder.potransnum' => SORT_ASC],
                        'desc' => ['purchaseorder.potransnum' => SORT_DESC],
                    ],
                    'slocid' => [
                        'asc' => ['ms_sloc.sloccode' => SORT_ASC],
                        'desc' => ['ms_sloc.sloccode' => SORT_DESC],
                    ], 'grtransnum', 'grtransdate'
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
        $query->joinWith('sloc');
        $query->joinWith('purchaseorder.plant.company');
        if (Groupmenuauth::getObject('company')) {
            $listCompany = Groupmenuauth::getObject('company');
        }
        if (Groupmenuauth::getObject('plant')) {
            $listPlant = Groupmenuauth::getObject('plant');
        }
        if ($this->slocid && $listSloc) {
            $query->andWhere("ms_sloc.slocid IN $listSloc");
        }
        if (Wfgroup::getMaxStatus('listgr')) {
            $maxStatus = Wfgroup::getMaxStatus('listgr');
        }
        if (Goodsreceiptdetail::getReturn()) {
            $listInvoice = Goodsreceiptdetail::getReturn();
        }
        $query->andWhere("ms_company.companyid IN $listCompany");
        $query->andWhere("ms_plant.plantid IN $listPlant");
        $query->andWhere("tr_goodsreceipt.status = $maxStatus");
        $query->andWhere("tr_goodsreceipt.id IN $listInvoice");
        $query->andFilterWhere([
            'id' => $this->id,
            'tr_goodsreceipt.status' => $this->status,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'createdBy' => $this->createdBy,
            'updatedBy' => $this->updatedBy,
        ]);

        $query->andFilterWhere(['like', 'grtransnum', $this->grtransnum])
            ->andFilterWhere(['like', 'sloccode', $this->slocid])
            ->andFilterWhere(['like', 'potransnum', $this->purchaseorderid])
            ->andFilterWhere(['>=', "DATE_FORMAT(tr_goodsreceipt.grtransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateFrom, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['<=', "DATE_FORMAT(tr_goodsreceipt.grtransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateTo, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['like', 'tr_goodsreceipt.headernote', $this->headernote]);

        return $dataProvider;
    }
    
    public static function findActive() {
        $maxStatus = Wfgroup::getMaxStatus('listgr');
        return self::find()->andWhere(self::tableName() . ".status = $maxStatus");
    }
}
