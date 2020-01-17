<?php

namespace app\modules\admin\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\models\Groupmenuauth;

/**
 * GroupmenuauthSearchModel represents the model behind the search form of `app\modules\admin\models\Groupmenuauth`.
 */
class GroupmenuauthSearchModel extends Groupmenuauth
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['groupmenuauthid'], 'integer'],
            [['menuvalueid', 'groupaccessid', 'menuauthid'], 'safe'],
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
        $query = Groupmenuauth::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['groupmenuauthid' => SORT_ASC],
                'attributes' => [
                    'menuauthid' => [
                        'asc' => ['ms_menuauth.menuobject' => SORT_ASC],
                        'desc' => ['ms_menuauth.menuobject' => SORT_DESC],
                    ],
                    'groupaccessid' => [
                        'asc' => ['ms_groupaccess.groupname' => SORT_ASC],
                        'desc' => ['ms_groupaccess.groupname' => SORT_DESC],
                    ], 'groupmenuauthid'
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
        $query->joinWith('menuauth');
        $query->joinWIth('groupaccess');
        $query->andFilterWhere([
            'groupmenuauthid' => $this->groupmenuauthid,
            'menuvalueid' => $this->menuvalueid,
        ]);

        $query->andFilterWhere(['like', 'menuobject', $this->menuauthid])
                ->andFilterWhere(['like', 'groupname', $this->groupaccessid]);

        return $dataProvider;
    }
}
