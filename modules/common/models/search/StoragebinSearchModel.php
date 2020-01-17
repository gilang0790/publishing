<?php

namespace app\modules\common\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use app\modules\admin\models\Groupmenuauth;
use app\modules\common\models\Storagebin;

/**
 * StoragebinSearchModel represents the model behind the search form of `app\modules\common\models\Storagebin`.
 */
class StoragebinSearchModel extends Storagebin
{
    public $name;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['storagebinid', 'ismultiproduct', 'status'], 'integer'],
            [['storagebinid', 'slocid', 'ismultiproduct', 'status', 'description'], 'safe'],
            [['qtymax'], 'number'],
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
        $query = Storagebin::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['slocid' => SORT_ASC],
                'attributes' => [
                    'slocid' => [
                        'asc' => ['ms_sloc.sloccode' => SORT_ASC],
                        'desc' => ['ms_sloc.sloccode' => SORT_DESC],
                    ], 'description'
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
        $query->andWhere("ms_company.companyid IN $listCompany");
        $query->andWhere("ms_plant.plantid IN $listPlant");
        $query->andWhere("ms_sloc.slocid IN $listSloc");
        $query->andFilterWhere([
            'storagebinid' => $this->storagebinid,
            'ismultiproduct' => $this->ismultiproduct,
            'qtymax' => $this->qtymax,
            'ms_storagebin.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'sloccode', $this->slocid])
                ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
    
    public static function dropdownList($slocid=null) {
        $sloc = -1;
        $listCompany = '(-1)';
        $listPlant = '(-1)';
        $listSloc = '(-1)';
        $query = self::find();
        $query->innerJoinWith('sloc.plant.company');
        if (Groupmenuauth::getObject('company')) {
            $listCompany = Groupmenuauth::getObject('company');
        }
        if (Groupmenuauth::getObject('plant')) {
            $listPlant = Groupmenuauth::getObject('plant');
        }
        if (Groupmenuauth::getObject('sloc')) {
            $listSloc = Groupmenuauth::getObject('sloc');
        }
        $query->andWhere("ms_company.companyid IN $listCompany");
        $query->andWhere("ms_plant.plantid IN $listPlant");
        $query->andWhere("ms_sloc.slocid IN $listSloc");
        if ($slocid) {
            $sloc = $slocid;
        }
        $query->andWhere(['ms_storagebin.slocid' => $sloc]);
        $query->andWhere(self::tableName() . '.status = true');
        return $query;
    }
    
    public static function findActive() {
        return self::find()->andWhere(self::tableName() . '.status = true');
    }
}
