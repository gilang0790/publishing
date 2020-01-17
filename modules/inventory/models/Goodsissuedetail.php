<?php

namespace app\modules\inventory\models;

use Yii;
use app\modules\inventory\models\Goodsissue;
use app\modules\common\models\Product;
use app\modules\common\models\Storagebin;
use app\modules\order\models\Salesorderdetail;
use app\modules\common\models\Unitofmeasure;

/**
 * This is the model class for table "goodsissuedetail".
 *
 * @property int $id
 * @property int $head_id
 * @property int $productid
 * @property int $unitofmeasureid
 * @property string $qty
 * @property int $storagebinid
 * @property int $salesorderdetailid
 * @property string $soqty
 * @property string $invqty
 * @property string $retqty
 * 
 * @property Goodsissue $goodsissue
 * @property Product $product
 * @property Storagebin $storagebin
 * @property Sodetail $sodetail
 * @property Unit $unit
 */
class Goodsissuedetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_goodsissuedetail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['head_id', 'productid', 'unitofmeasureid'], 'required'],
            [['head_id', 'productid', 'unitofmeasureid', 'storagebinid', 'salesorderdetailid'], 'integer'],
            [['qty', 'soqty', 'invqty', 'retqty'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'head_id' => 'Head ID',
            'productid' => 'Barang',
            'unitofmeasureid' => 'Satuan',
            'qty' => 'Jumlah',
            'storagebinid' => 'Rak',
            'salesorderdetailid' => 'ID Sales Order Detail',
            'soqty' => 'Jumlah Sales Order',
            'invqty' => 'Jumlah Invoice',
            'retqty' => 'Jumlah Retur',
        ];
    }
    
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['productid' => 'productid']);
    }
    
    public function getGoodsissue()
    {
        return $this->hasOne(Goodsissue::className(), ['id' => 'head_id']);
    }
    
    public function getStoragebin()
    {
        return $this->hasOne(Storagebin::className(), ['storagebinid' => 'storagebinid']);
    }
    
    public function getSodetail()
    {
        return $this->hasOne(Salesorderdetail::className(), ['id' => 'salesorderdetailid']);
    }
    
    public function getUnit()
    {
        return $this->hasOne(Unitofmeasure::className(), ['unitofmeasureid' => 'unitofmeasureid']);
    }
    
    public function afterFind() {
        parent::afterFind();
        $this->qty = round($this->qty);
    }
    
    public static function getOutstanding() {
        $model = self::find()
            ->select(['tr_goodsissuedetail.head_id as head_id'])
            ->where("(COALESCE(tr_goodsissuedetail.invqty, 0) + COALESCE(tr_goodsissuedetail.retqty, 0)) < tr_goodsissuedetail.qty")
            ->asArray()
            ->all();
        
        $strArray = [];
        $result = '';
        if ($model) {
            foreach ($model as $data) {
                if (!in_array($data['head_id'], $strArray)) {
                    $strArray[] = $data['head_id'];
                }
            }
            
            if ($strArray) {
                foreach ($strArray as $val) {
                    if ($val == end($strArray)) {
                        $result .= "'$val'";
                    } else {
                        $result .= "'$val',";
                    }
                }
            }
            if ($strArray) {
                return '('.$result.')';
            }
        }
        return $result;
    }
    
    public static function getOutstandingDetail() {
        $model = self::find()
            ->select(['tr_goodsissuedetail.id as id'])
            ->where("(COALESCE(tr_goodsissuedetail.invqty, 0) + COALESCE(tr_goodsissuedetail.retqty, 0)) < tr_goodsissuedetail.qty")
            ->asArray()
            ->all();
        
        $strArray = [];
        $result = '';
        if ($model) {
            foreach ($model as $data) {
                if (!in_array($data['id'], $strArray)) {
                    $strArray[] = $data['id'];
                }
            }
            
            if ($strArray) {
                foreach ($strArray as $val) {
                    if ($val == end($strArray)) {
                        $result .= "'$val'";
                    } else {
                        $result .= "'$val',";
                    }
                }
            }
            if ($strArray) {
                return '('.$result.')';
            }
        }
        return $result;
    }
    
    public static function getProductList($goodsissueid) {
        $model = self::find()
            ->select(['tr_goodsissuedetail.productid as product'])
            ->andWhere(['=', 'tr_goodsissuedetail.head_id', $goodsissueid])
            ->asArray()
            ->all();
        
        $strArray = [];
        $result = '';
        if ($model) {
            foreach ($model as $data) {
                $strArray[] = $data['product'];
            }
            
            if ($strArray) {
                foreach ($strArray as $val) {
                    if ($val == end($strArray)) {
                        $result .= "'$val'";
                    } else {
                        $result .= "'$val',";
                    }
                }
            }
            if ($strArray) {
                return '('.$result.')';
            }
        }
        return $result;
    }
    
    public static function getProductIssueList($goodsissueid) {
        $model = self::find()
            ->select(['tr_goodsissuedetail.productid as product'])
            ->andWhere(['=', 'tr_goodsissuedetail.head_id', $goodsissueid])
            ->andWhere("(COALESCE(tr_goodsissuedetail.invqty, 0) + COALESCE(tr_goodsissuedetail.retqty, 0)) < tr_goodsissuedetail.qty")
            ->asArray()
            ->all();
        
        $strArray = [];
        $result = '';
        if ($model) {
            foreach ($model as $data) {
                $strArray[] = $data['product'];
            }
            
            if ($strArray) {
                foreach ($strArray as $val) {
                    if ($val == end($strArray)) {
                        $result .= "'$val'";
                    } else {
                        $result .= "'$val',";
                    }
                }
            }
            if ($strArray) {
                return '('.$result.')';
            }
        }
        return $result;
    }
    
    public static function getProductIssueReturnList($goodsissueid) {
        $model = self::find()
            ->select(['tr_goodsissuedetail.productid as product'])
            ->andWhere(['=', 'tr_goodsissuedetail.head_id', $goodsissueid])
            ->andWhere("COALESCE(tr_goodsissuedetail.retqty, 0) < tr_goodsissuedetail.qty")
            ->asArray()
            ->all();
        
        $strArray = [];
        $result = '';
        if ($model) {
            foreach ($model as $data) {
                $strArray[] = $data['product'];
            }
            
            if ($strArray) {
                foreach ($strArray as $val) {
                    if ($val == end($strArray)) {
                        $result .= "'$val'";
                    } else {
                        $result .= "'$val',";
                    }
                }
            }
            if ($strArray) {
                return '('.$result.')';
            }
        }
        return $result;
    }
    
    public static function getReturn() {
        $model = self::find()
            ->select(['tr_goodsissuedetail.head_id as head_id'])
            ->where("(COALESCE(tr_goodsissuedetail.retqty, 0)) < tr_goodsissuedetail.qty")
            ->asArray()
            ->all();
        
        $strArray = [];
        $result = '';
        if ($model) {
            foreach ($model as $data) {
                if (!in_array($data['head_id'], $strArray)) {
                    $strArray[] = $data['head_id'];
                }
            }
            
            if ($strArray) {
                foreach ($strArray as $val) {
                    if ($val == end($strArray)) {
                        $result .= "'$val'";
                    } else {
                        $result .= "'$val',";
                    }
                }
            }
            if ($strArray) {
                return '('.$result.')';
            }
        }
        return $result;
    }
    
    public static function getReturnDetail($head_id) {
        $model = Goodsissuedetail::find()
            ->select(['tr_goodsissuedetail.id as id'])
            ->where("(COALESCE(tr_goodsissuedetail.retqty, 0)) < tr_goodsissuedetail.qty AND tr_goodsissuedetail.head_id = $head_id")
            ->asArray()
            ->all();
        
        $strArray = [];
        $result = '';
        if ($model) {
            foreach ($model as $data) {
                if (!in_array($data['id'], $strArray)) {
                    $strArray[] = $data['id'];
                }
            }
            
            if ($strArray) {
                foreach ($strArray as $val) {
                    if ($val == end($strArray)) {
                        $result .= "'$val'";
                    } else {
                        $result .= "'$val',";
                    }
                }
            }
            if ($strArray) {
                return '('.$result.')';
            }
        }
        return $result;
    }
}
