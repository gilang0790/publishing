<?php

namespace app\modules\common\models\search;

use app\components\ReportEngine;
use app\modules\common\models\Product;
use app\modules\inventory\models\Goodsissuedetail;
use app\modules\inventory\models\Goodsreceiptdetail;
use app\modules\order\models\Salesorderdetail;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;

/**
 * ProductSearchModel represents the model behind the search form of `app\modules\common\models\Product`.
 */
class ProductSearchModel extends Product
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['productid', 'status', 'type'], 'integer'],
            [['productname', 'categoryid', 'unitofmeasureid', 'productcode', 'isbn', 'author', 'size', 'weight', 'notes'], 'safe'],
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
        $query = Product::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['productname' => SORT_ASC],
                'attributes' => [
                    'categoryid' => [
                        'asc' => ['ms_category.categoryname' => SORT_ASC],
                        'desc' => ['ms_category.categoryname' => SORT_DESC],
                    ],
                    'unitofmeasureid' => [
                        'asc' => ['ms_unitofmeasure.uomcode' => SORT_ASC],
                        'desc' => ['ms_unitofmeasure.uomcode' => SORT_DESC],
                    ], 'productname', 'productcode', 'isbn', 'author'
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
        $query->joinWith('category');
        $query->joinWith('uom');
        $query->andFilterWhere([
            'productid' => $this->productid,
            'ms_product.type' => $this->type,
            'ms_product.status' => $this->status
        ]);

        $query->andFilterWhere(['like', 'productname', $this->productname])
            ->andFilterWhere(['like', 'productcode', $this->productcode])
            ->andFilterWhere(['like', 'isbn', $this->isbn])
            ->andFilterWhere(['like', 'author', $this->author])
            ->andFilterWhere(['like', 'size', $this->size])
            ->andFilterWhere(['like', 'weight', $this->weight])
            ->andFilterWhere(['like', 'notes', $this->notes])
            ->andFilterWhere(['like', 'categoryname', $this->categoryid])
            ->andFilterWhere(['like', 'uomcode', $this->unitofmeasureid]);

        return $dataProvider;
    }
    
    public static function findProductSales($salesorderid) {
        $listProduct = Salesorderdetail::getProductList($salesorderid);
        return self::find()
            ->andWhere("ms_product.productid IN $listProduct")
            ->andWhere(["type" => Product::STOCK]);
    }
    
    public static function findProductIssue($goodsissueid) {
        $listProduct = Goodsissuedetail::getProductIssueList($goodsissueid);
        return self::find()
            ->andWhere("ms_product.productid IN $listProduct")
            ->andWhere(["type" => Product::STOCK]);
    }
    
    public static function findProductIssueReturn($goodsissueid) {
        $listProduct = Goodsissuedetail::getProductIssueReturnList($goodsissueid);
        return self::find()
            ->andWhere("ms_product.productid IN $listProduct")
            ->andWhere(["type" => Product::STOCK]);
    }
    
    public static function findProductReceipt($goodsreceiptid) {
        $listProduct = Goodsreceiptdetail::getProductReceiptList($goodsreceiptid);
        return self::find()
            ->andWhere("ms_product.productid IN $listProduct")
            ->andWhere(["type" => Product::STOCK]);
    }
    
    public static function getUomID($id) {
        $model = self::findActive()
            ->andWhere(['productid' => $id])
            ->one();
        if ($model) {
            return $model->unitofmeasureid;
        }
        return "";
    }
    
    public function exportData() {
        $query = (new Query)
            ->select([
                'ms_product.productid',
                'ms_product.productname',
                'ms_product.productcode',
                'ms_unitofmeasure.uomcode',
                'ms_category.categoryname',
                'ms_product.isbn',
                'ms_product.author',
                'ms_product.weight',
                'ms_product.size',
                'type' => new Expression("CASE WHEN ms_product.type = 1 THEN 'Barang' ELSE 'Jasa' END"),
                'status' => new Expression("CASE WHEN ms_product.status = 1 THEN 'Aktif' ELSE 'Tidak Aktif' END")
            ])
            ->from("ms_product")
            ->leftJoin("ms_unitofmeasure", 
                "ms_unitofmeasure.unitofmeasureid = ms_product.unitofmeasureid")
            ->leftJoin("ms_category",
                "ms_category.categoryid = ms_product.categoryid")
            ->where(['ms_product.status' => Product::STATUS_ACTIVE])
            ->orderBy("ms_product.productname ASC, ms_product.productid, ms_product.type DESC");
        
        $columnDefinitions = [
            "productid" => [
                "label" => $this->getAttributeLabel("productid"),
                "type" => "string"
            ],
            "productname" => [
                "label" => $this->getAttributeLabel("productname"),
                "type" => "string"
            ],
            "productcode" => [
                "label" => $this->getAttributeLabel("productcode"),
                "type" => "string"
            ],
            "uomcode" => [
                "label" => $this->getAttributeLabel("unitofmeasureid"),
                "type" => "string"
            ],
            "categoryname" => [
                "label" => $this->getAttributeLabel("categoryid"),
                "type" => "string"
            ],
            "isbn" => [
                "label" => $this->getAttributeLabel("isbn"),
                "type" => "string"
            ],
            "author" => [
                "label" => $this->getAttributeLabel("author"),
                "type" => "string"
            ],
            "weight" => [
                "label" => $this->getAttributeLabel("weight"),
                "type" => "string"
            ],
            "size" => [
                "label" => $this->getAttributeLabel("size"),
                "type" => "string"
            ],
            "type" => [
                "label" => $this->getAttributeLabel("type"),
                "type" => "string"
            ],
            "status" => [
                "label" => $this->getAttributeLabel("status"),
                "type" => "string"
            ]
        ];
        
        ReportEngine::downloadReport($this, $query, $columnDefinitions,
            "Data Barang");
    }
    
    public static function findActive() {
        return self::find()->andWhere(self::tableName() . '.status = true');
    }
}
