<?php

namespace app\modules\inventory\models;

use Yii;
use app\modules\common\models\Product;
use app\modules\common\models\Storagebin;
use app\modules\purchase\models\Purchaseorderdetail;
use app\modules\common\models\Unitofmeasure;

/**
 * This is the model class for table "tr_goodsreceiptdetail".
 *
 * @property int $id
 * @property int $head_id FK tr_goodreceipt (id)
 * @property int $productid
 * @property int $unitofmeasureid
 * @property string $qty
 * @property string $freeqty
 * @property int $storagebinid FK storagebin (storagebinid)
 * @property int $purchaseorderdetailid
 * @property string $poqty
 * @property string $invqty
 * @property string $retqty
 * 
 * @property Goodsreceipt $goodsreceipt
 * @property Product $product
 * @property Storagebin $storagebin
 * @property Podetail $podetail
 * @property Unit $unit
 */
class Goodsreceiptdetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_goodsreceiptdetail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['head_id', 'productid', 'unitofmeasureid'], 'required'],
            [['head_id', 'productid', 'unitofmeasureid', 'storagebinid', 'purchaseorderdetailid'], 'integer'],
            [['qty', 'freeqty', 'poqty', 'invqty', 'retqty'], 'number'],
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
            'freeqty' => 'Jumlah Gratis',
            'storagebinid' => 'Rak',
            'purchaseorderdetailid' => 'ID Detail Pembelian',
            'poqty' => 'Jumlah PO',
            'invqty' => 'Jumlah Invoice',
            'retqty' => 'Jumlah Retur',
        ];
    }
    
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['productid' => 'productid']);
    }
    
    public function getGoodsreceipt()
    {
        return $this->hasOne(Goodsreceipt::className(), ['id' => 'head_id']);
    }
    
    public function getStoragebin()
    {
        return $this->hasOne(Storagebin::className(), ['storagebinid' => 'storagebinid']);
    }
    
    public function getPodetail()
    {
        return $this->hasOne(Purchaseorderdetail::className(), ['id' => 'purchaseorderdetailid']);
    }
    
    public function getUnit()
    {
        return $this->hasOne(Unitofmeasure::className(), ['unitofmeasureid' => 'unitofmeasureid']);
    }
    
    public function afterFind() {
        parent::afterFind();
        $this->qty = round($this->qty);
        $this->freeqty = round($this->freeqty);
    }
    
    public static function getOutstanding() {
        $model = self::find()
            ->select(['tr_goodsreceiptdetail.head_id as head_id'])
            ->where("(COALESCE(tr_goodsreceiptdetail.invqty, 0) + COALESCE(tr_goodsreceiptdetail.retqty, 0)) < tr_goodsreceiptdetail.qty")
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
            ->select(['tr_goodsreceiptdetail.id as id'])
            ->where("(COALESCE(tr_goodsreceiptdetail.invqty, 0) + COALESCE(tr_goodsreceiptdetail.retqty, 0)) < tr_goodsreceiptdetail.qty")
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
    
    public static function getProductList($goodsreceiptid) {
        $model = self::find()
            ->select(['tr_goodsreceiptdetail.productid as product'])
            ->andWhere(['=', 'tr_goodsreceiptdetail.head_id', $goodsreceiptid])
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
    
    public static function getProductReceiptList($goodsissueid) {
        $model = self::find()
            ->select(['tr_goodsreceiptdetail.productid as product'])
            ->andWhere(['=', 'tr_goodsreceiptdetail.head_id', $goodsissueid])
            ->andWhere("COALESCE(tr_goodsreceiptdetail.retqty, 0) < tr_goodsreceiptdetail.qty")
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
            ->select(['tr_goodsreceiptdetail.head_id as head_id'])
            ->where("(COALESCE(tr_goodsreceiptdetail.retqty, 0)) < tr_goodsreceiptdetail.qty")
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
}
