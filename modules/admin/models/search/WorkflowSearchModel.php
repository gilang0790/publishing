<?php

namespace app\modules\admin\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\models\Workflow;

/**
 * WorkflowSearchModel represents the model behind the search form of `app\modules\admin\models\Workflow`.
 */
class WorkflowSearchModel extends Workflow
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['workflowid', 'wfminstat', 'wfmaxstat', 'status'], 'integer'],
            [['wfname', 'wfdesc'], 'safe'],
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
        $query = Workflow::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'workflowid' => $this->workflowid,
            'wfminstat' => $this->wfminstat,
            'wfmaxstat' => $this->wfmaxstat,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'wfname', $this->wfname])
            ->andFilterWhere(['like', 'wfdesc', $this->wfdesc]);

        return $dataProvider;
    }
    
    public static function findActive() {
        return self::find()->andWhere(self::tableName() . '.status = true');
    }
}
