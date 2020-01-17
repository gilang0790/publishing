<?php

namespace app\modules\admin\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\models\Usergroup;

/**
 * UsergroupSearchModel represents the model behind the search form of `app\modules\admin\models\Usergroup`.
 */
class UsergroupSearchModel extends Usergroup
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usergroupid'], 'integer'],
            [['usergroupid', 'userID', 'groupaccessid'], 'safe'],
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
        $query = Usergroup::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['usergroupid' => SORT_ASC],
                'attributes' => [
                    'userID' => [
                        'asc' => ['ms_user.username' => SORT_ASC],
                        'desc' => ['ms_user.username' => SORT_DESC],
                    ],
                    'groupaccessid' => [
                        'asc' => ['ms_groupaccess.groupname' => SORT_ASC],
                        'desc' => ['ms_groupaccess.groupname' => SORT_DESC],
                    ], 'usergroupid'
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
        $query->joinWith('user');
        $query->joinWIth('groupaccess');
        $query->andFilterWhere(['like', 'username', $this->userID])
            ->andFilterWhere(['like', 'groupname', $this->groupaccessid]);

        return $dataProvider;
    }
}
