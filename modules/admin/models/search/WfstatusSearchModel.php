<?php

namespace app\modules\admin\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\models\Wfstatus;

/**
 * WfstatusSearchModel represents the model behind the search form of `app\modules\admin\models\Wfstatus`.
 */
class WfstatusSearchModel extends Wfstatus
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wfstatusid', 'wfstat'], 'integer'],
            [['workflowid', 'wfstatusname'], 'safe'],
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
        $query = Wfstatus::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['wfstatusid' => SORT_ASC],
                'attributes' => [
                    'workflowid' => [
                        'asc' => ['ms_workflow.wfname' => SORT_ASC],
                        'desc' => ['ms_workflow.wfname' => SORT_DESC],
                    ], 'wfstatusid'
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
        $query->andFilterWhere([
            'wfstatusid' => $this->wfstatusid,
            'wfstat' => $this->wfstat,
        ]);

        $query->andFilterWhere(['like', 'wfstatusname', $this->wfstatusname])
                ->andFilterWhere(['like', 'wfdesc', $this->workflowid]);

        return $dataProvider;
    }
}
