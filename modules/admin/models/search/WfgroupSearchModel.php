<?php

namespace app\modules\admin\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\models\Wfgroup;

/**
 * WfgroupSearchModel represents the model behind the search form of `app\modules\admin\models\Wfgroup`.
 */
class WfgroupSearchModel extends Wfgroup
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wfgroupid', 'wfbefstat', 'wfrecstat'], 'integer'],
            [['wfgroupid', 'workflowid', 'groupaccessid', 'wfbefstat', 'wfrecstat'], 'safe'],
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
        $query = Wfgroup::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['wfgroupid' => SORT_ASC],
                'attributes' => [
                    'workflowid' => [
                        'asc' => ['ms_workflow.wfname' => SORT_ASC],
                        'desc' => ['ms_workflow.wfname' => SORT_DESC],
                    ],
                    'groupaccessid' => [
                        'asc' => ['ms_groupaccess.groupname' => SORT_ASC],
                        'desc' => ['ms_groupaccess.groupname' => SORT_DESC],
                    ], 'wfgroupid'
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
        $query->joinWith('workflow');
        $query->joinWIth('groupaccess');
        $query->andFilterWhere([
            'wfgroupid' => $this->wfgroupid,
            'wfbefstat' => $this->wfbefstat,
            'wfrecstat' => $this->wfrecstat,
        ]);
        $query->andFilterWhere(['like', 'wfdesc', $this->workflowid])
            ->andFilterWhere(['like', 'groupname', $this->groupaccessid]);

        return $dataProvider;
    }
}
