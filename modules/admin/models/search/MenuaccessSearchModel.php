<?php

namespace app\modules\admin\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\models\Menuaccess;
use app\modules\admin\models\Modules;

/**
 * MenuaccessSearchModel represents the model behind the search form of `app\modules\admin\models\Menuaccess`.
 */
class MenuaccessSearchModel extends Menuaccess
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['menuaccessid', 'sortorder', 'status'], 'integer'],
            [['menuname', 'parentid', 'moduleid', 'description', 'menuurl', 'menuicon'], 'safe'],
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
        $query = Menuaccess::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['sortorder' => SORT_ASC],
                'attributes' => [
                    'moduleid' => [
                        'asc' => ['ms_modules.modulename' => SORT_ASC],
                        'desc' => ['ms_modules.modulename' => SORT_DESC],
                    ], 'sortorder'
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
        $query->joinWith(['menuparent' => function($query) { $query->from(['menuparent' => 'ms_menuaccess']); }]);
        $query->joinWIth('module');
        $query->andFilterWhere([
            'ms_menuaccess.menuaccessid' => $this->menuaccessid,
            'ms_menuaccess.description' => $this->description,
            'ms_menuaccess.menuurl' => $this->menuurl,
            'ms_menuaccess.sortorder' => $this->sortorder,
            'ms_menuaccess.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'menuparent.description', $this->parentid])
            ->andFilterWhere(['like', 'ms_menuaccess.menuname', $this->menuname])
            ->andFilterWhere(['like', 'moduledesc', $this->moduleid]);

        return $dataProvider;
    }
    
    public static function findActive() {
        return self::find()->andWhere(self::tableName() . '.status = true');
    }
    
    public static function getMenuList()
    {
        return $moduleModel = Modules::find()->where("status = 1")->asArray()->all();
    }
    
    public static function getSubmenuList($moduleid)
    {
        $model = self::find()->where("moduleid = :moduleid AND status = 1", [':moduleid' => $moduleid])
            ->asArray()
            ->all();
        if ($model) {
            return $model;
        }
        return NULL;
    }
}
