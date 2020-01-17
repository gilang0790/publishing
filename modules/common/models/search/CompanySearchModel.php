<?php

namespace app\modules\common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\common\models\Company;
use app\modules\admin\models\Groupmenuauth;

/**
 * CompanySearchModel represents the model behind the search form of `app\modules\common\models\Company`.
 */
class CompanySearchModel extends Company
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['companyid', 'isholding', 'status'], 'integer'],
            [['companyname', 'cityid', 'companycode', 'address', 'zipcode', 'phoneno', 'webaddress', 'email'], 'safe'],
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
        $query = Company::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['companyid' => SORT_ASC],
                'attributes' => [
                    'cityid' => [
                        'asc' => ['ms_city.cityname' => SORT_ASC],
                        'desc' => ['ms_city.cityname' => SORT_DESC],
                    ], 'companyid', 'companyname', 'companycode'
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
        $query->joinWith('city');
        if (Groupmenuauth::getObject('company')) {
            $listCompany = Groupmenuauth::getObject('company');
        }
        $query->andWhere("ms_company.companyid IN $listCompany");
        $query->andFilterWhere([
            'companyid' => $this->companyid,
            'isholding' => $this->isholding,
            'ms_company.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'companyname', $this->companyname])
            ->andFilterWhere(['like', 'cityname', $this->cityid])
            ->andFilterWhere(['like', 'companycode', $this->companycode])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'zipcode', $this->zipcode])
            ->andFilterWhere(['like', 'phoneno', $this->phoneno])
            ->andFilterWhere(['like', 'webaddress', $this->webaddress])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
    
    public static function findActive() {
        return self::find()->andWhere(self::tableName() . '.status = true');
    }
}
