<?php

namespace app\modules\inventory\models\search;

use Yii;
use yii\base\Model;
use yii\db\Expression;
use yii\data\ActiveDataProvider;
use app\modules\inventory\models\Stock;
use app\modules\common\models\Category;
use app\modules\common\models\Product;
use app\modules\common\models\Plant;
use app\modules\common\models\Sloc;
use app\modules\common\models\Unitofmeasure;

/**
 * StockSearchModel represents the model behind the search form of `app\modules\inventory\models\Stock`.
 */
class StockSearchModel extends Stock
{   
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['stockcardid', 'stockid', 'productid', 'unitofmeasureid', 'slocid', 'storagebinid', 'createdBy', 'updatedBy'], 'integer'],
            [['categoryid', 'plantid', 'plantcode', 'productcode', 'productname', 'sloccode', 'transdate', 'refnum', 'transtype', 
                'createdAt', 'updatedAt'], 'safe'],
            [['qtyin', 'qtyout', 'hpp', 'buyprice', 'stockValue'], 'number'],
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
    public function searchStock($isDownload = false)
    {
        $wherePlantCond = $this->plantid ==  [] ? ['e.plantid' => 0] : ['IN', 'e.plantid', $this->plantid];
        $table = Stock::find();
        $query = $table
                ->alias('a')
                ->select([
                    'e.plantid',
                    'e.plantcode',
                    'a.slocid',
                    'd.sloccode',
                    'f.categoryid',
                    'f.categoryname',
                    'b.productname',
                    'b.productcode',
                    'c.uomcode',
                    'a.qty',
                    'a.hpp',
                    'stockValue' => new Expression('a.qty * a.hpp')
                ])
                ->innerJoin(Product::tableName() . ' b', 'b.productID = a.productID')
                ->innerJoin(Unitofmeasure::tableName() . ' c', 'c.unitofmeasureid = b.unitofmeasureid')
                ->innerJoin(Sloc::tableName() . ' d', 'd.slocid = a.slocid')
                ->innerJoin(Plant::tableName() . ' e', 'e.plantid = d.plantid')
                ->innerJoin(Category::tableName() . ' f', 'f.categoryid = b.categoryid')
                ->where(['=', 'b.type', Product::STOCK])
                ->andWhere(['=', 'b.status', Product::STATUS_ACTIVE])
                ->andWhere($wherePlantCond);
                if ($this->slocid) {
                    $query->andWhere(['IN', 'a.slocid', $this->slocid]);
                }
                $query
                ->andFilterWhere(['like', 'b.productname', $this->productname])
                ->andFilterWhere(['like', 'b.productcode', $this->productcode])
                ->andFilterWhere(['like', 'b.categoryid', $this->categoryid])
                ->groupBy([
                    'e.plantid',
                    'e.plantcode',
                    'a.slocid',
                    'd.sloccode',
                    'f.categoryid',
                    'f.categoryname',
                    'b.productname',
                    'b.productcode',
                    'c.uomcode',
                    'a.qty',
                    'a.hpp'
                ])
                ->orderBy('e.plantcode, d.sloccode, f.categoryname, b.productname');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        return $dataProvider;
    }
}
