<?php

namespace app\modules\accounting\models\search;

use app\components\AppHelper;
use app\modules\admin\models\Groupmenuauth;
use app\modules\accounting\models\Invoicear;
use app\modules\admin\models\Wfgroup;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class InvoicearBrowseModel extends Invoicear
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
            [['artransdate', 'plantid', 'artransnum', 'addressbookid', 'aramount', 'payamount', 'goodsissueid', 'headernote', 
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
        $query = Invoicear::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['artransnum' => SORT_DESC],
                'attributes' => [
                    'plantid' => [
                        'asc' => ['ms_plant.plantcode' => SORT_ASC],
                        'desc' => ['ms_plant.plantcode' => SORT_DESC],
                    ],
                    'addressbookid' => [
                        'asc' => ['ms_addressbook.fullname' => SORT_ASC],
                        'desc' => ['ms_addressbook.fullname' => SORT_DESC],
                    ],
                    'goodsissueid' => [
                        'asc' => ['tr_goodsissue.gitransnum' => SORT_ASC],
                        'desc' => ['tr_goodsissue.gitransnum' => SORT_DESC],
                    ], 'artransnum', 'artransdate', 'aramount', 'payamount'
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
        $query->joinWith('goodsissue');
        if (Groupmenuauth::getObject('company')) {
            $listCompany = Groupmenuauth::getObject('company');
        }
        if (Groupmenuauth::getObject('plant')) {
            $listPlant = Groupmenuauth::getObject('plant');
        }
        if (Wfgroup::getMaxStatus('listar')) {
            $maxStatus = Wfgroup::getMaxStatus('listar');
        }
        $query->andWhere("ms_company.companyid IN $listCompany");
        $query->andWhere("ms_plant.plantid IN $listPlant");
        $query->andWhere("tr_invoicear.status = $maxStatus");
        $query->andWhere("COALESCE(tr_invoicear.payamount, 0) < COALESCE(tr_invoicear.aramount, 0)");
        $query->andFilterWhere([
            'id' => $this->id,
            'tr_invoicear.status' => $this->status,
            'aramount' => $this->aramount,
            'payamount' => $this->payamount,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'createdBy' => $this->createdBy,
            'updatedBy' => $this->updatedBy,
        ]);

        $query->andFilterWhere(['like', 'artransnum', $this->artransnum])
            ->andFilterWhere(['like', 'ms_plant.plantcode', $this->plantid])
            ->andFilterWhere(['like', 'tr_goodsissue.gitransnum', $this->goodsissueid])
            ->andFilterWhere(['like', 'ms_addressbook.fullname', $this->addressbookid])
            ->andFilterWhere(['>=', "DATE_FORMAT(tr_invoicear.artransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateFrom, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['<=', "DATE_FORMAT(tr_invoicear.artransdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateTo, 'd-m-Y', 'Y-m-d')])
            ->andFilterWhere(['like', 'tr_invoicear.headernote', $this->headernote]);

        return $dataProvider;
    }
    
    public static function findActive() {
        $maxStatus = Wfgroup::getMaxStatus('listar');
        return self::find()->andWhere(self::tableName() . ".status = $maxStatus");
    }
}
