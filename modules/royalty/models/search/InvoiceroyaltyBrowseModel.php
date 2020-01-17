<?php

namespace app\modules\royalty\models\search;

use app\components\AppHelper;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\models\Groupmenuauth;
use app\modules\royalty\models\Invoiceroyalty;
use app\modules\admin\models\Wfgroup;

/**
 * InvoiceroyaltyBrowseModel represents the model behind the search form of `app\modules\royalty\models\Invoiceroyalty`.
 */
class InvoiceroyaltyBrowseModel extends Invoiceroyalty
{
    public $dateFrom;
    public $dateTo;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'royaltysettingid', 'createdBy', 'updatedBy'], 'integer'],
            [['plantid', 'transdate', 'transnum', 'addressbookid', 'productid', 'createdAt', 'updatedAt', 'dateFrom', 'dateTo'], 'safe'],
            [['totalqty', 'amount', 'payamount'], 'number'],
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
        $query = Invoiceroyalty::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['transdate' => SORT_ASC],
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
                    ], 'transdate', 'transnum'
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
        $query->andWhere("ms_company.companyid IN $listCompany");
        $query->andWhere("ms_plant.plantid IN $listPlant");
        $query->andWhere("tr_invoiceroyalty.payamount < tr_invoiceroyalty.amount");
        $query->andFilterWhere([
            'id' => $this->id,
            'royaltysettingid' => $this->royaltysettingid,
            'totalqty' => $this->totalqty,
            'amount' => $this->amount,
            'payamount' => $this->payamount,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'createdBy' => $this->createdBy,
            'updatedBy' => $this->updatedBy,
        ]);
        
        $query->andFilterWhere(['like', 'plantcode', $this->plantid])
            ->andFilterWhere(['like', 'transnum', $this->transnum])
            ->andFilterWhere(['like', 'fullname', $this->addressbookid])
            ->andFilterWhere(['like', 'productname', $this->productid])
            ->andFilterWhere(['>=', "DATE_FORMAT(tr_invoiceroyalty.transdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateFrom, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['<=', "DATE_FORMAT(tr_invoiceroyalty.transdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateTo, 'd-m-Y', 'Y-m-d')]);

        return $dataProvider;
    }
    
    public static function findOutstanding() {
        return self::find()->andWhere("tr_invoiceroyalty.payamount < tr_invoiceroyalty.amount");
    }
}
