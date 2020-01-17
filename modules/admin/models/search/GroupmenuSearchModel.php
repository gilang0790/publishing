<?php

namespace app\modules\admin\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\models\Groupmenu;

/**
 * GroupmenuSearchModel represents the model behind the search form of `app\modules\admin\models\Groupmenu`.
 */
class GroupmenuSearchModel extends Groupmenu
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['groupmenuid', 'isread', 'iswrite', 'ispost', 'isreject', 'isupload', 'isdownload', 'ispurge'], 'integer'],
            [['groupmenuid', 'groupaccessid', 'menuaccessid'], 'safe'],
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
        $query = Groupmenu::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['groupmenuid' => SORT_ASC],
                'attributes' => [
                    'menuaccessid' => [
                        'asc' => ['ms_menuaccess.description' => SORT_ASC],
                        'desc' => ['ms_menuaccess.description' => SORT_DESC],
                    ],
                    'groupaccessid' => [
                        'asc' => ['ms_groupaccess.groupname' => SORT_ASC],
                        'desc' => ['ms_groupaccess.groupname' => SORT_DESC],
                    ], 'groupmenuid'
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
        $query->joinWith('menuaccess');
        $query->joinWIth('groupaccess');
        $query->andFilterWhere([
            'groupmenuid' => $this->groupmenuid,
            'isread' => $this->isread,
            'iswrite' => $this->iswrite,
            'ispost' => $this->ispost,
            'isreject' => $this->isreject,
            'isupload' => $this->isupload,
            'isdownload' => $this->isdownload,
            'ispurge' => $this->ispurge,
        ]);
        $query->andFilterWhere(['like', 'description', $this->menuaccessid])
            ->andFilterWhere(['like', 'groupname', $this->groupaccessid]);

        return $dataProvider;
    }
    
    public static function getValue($groupaccessid, $menuaccessid, $col)
    {
        $model = self::find()->where("groupaccessid = :groupaccessid AND menuaccessid = :menuaccessid", 
                        [':groupaccessid'=>$groupaccessid, ':menuaccessid'=>$menuaccessid])->one();
        if ($model) {
            return $model->$col;
        }
        return null;
    }
}
