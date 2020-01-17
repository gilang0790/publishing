<?php

namespace app\modules\inventory\models\search;

use Yii;
use yii\db\Expression;
use app\components\AppHelper;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\inventory\models\Stockcard;
use app\modules\common\models\Category;
use app\modules\common\models\Product;
use app\modules\common\models\Plant;
use app\modules\common\models\Sloc;
use app\modules\common\models\Unitofmeasure;

/**
 * StockCardSearchModel represents the model behind the search form of `app\modules\inventory\models\Stockcard`.
 */
class StockCardSearchModel extends Stockcard
{   
    public $dateFrom;
    public $dateTo;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['stockcardid', 'stockid', 'productid', 'unitofmeasureid', 'slocid', 'storagebinid', 'createdBy', 'updatedBy'], 'integer'],
            [['categoryid', 'plantid', 'plantcode', 'productcode', 'productname', 'sloccode', 'transdate', 'refnum', 'transtype', 
                'createdAt', 'updatedAt', 'dateFrom', 'dateTo'], 'safe'],
            [['qtyin', 'qtyout', 'hpp', 'buyprice', 'stockQty', 'stockValue'], 'number'],
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
    public function searchStockPeriod($isDownload = false)
    {
        $wherePlantCond = $this->plantid ==  [] ? ['e.plantid' => 0] : ['IN', 'e.plantid', $this->plantid];
        $table = Stockcard::find();
        $query = $table
                ->alias('a')
                ->select([
                    'a.transdate',
                    'e.plantid',
                    'e.plantcode',
                    'a.slocid',
                    'd.sloccode',
                    'f.categoryname',
                    'b.productname',
                    'b.productcode',
                    'c.uomcode',
                    'a.refnum',
                    'a.qtyin',
                    'a.qtyout'
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
                ->andFilterWhere(['>=', "DATE_FORMAT(a.transdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateFrom, 'd-m-Y', 'Y-m-d')])
                ->andFilterWhere(['<=', "DATE_FORMAT(a.transdate, '%Y-%m-%d')", AppHelper::convertDateTimeFormat($this->dateTo, 'd-m-Y', 'Y-m-d')])
                ->groupBy([
                    'a.transdate',
                    'e.plantid',
                    'e.plantcode',
                    'a.slocid',
                    'd.sloccode',
                    'f.categoryname',
                    'b.productname',
                    'b.productcode',
                    'c.uomcode',
                    'a.refnum',
                    'a.qtyin',
                    'a.qtyout'
                ])
                ->orderBy('a.transdate, e.plantcode, d.sloccode, b.productname');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        return $dataProvider;
    }
}
