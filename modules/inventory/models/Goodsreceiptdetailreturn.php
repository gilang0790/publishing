<?php

namespace app\modules\inventory\models;

use Yii;
use app\modules\inventory\models\Goodsreceiptreturn;
use app\modules\common\models\Product;
use app\modules\common\models\Storagebin;
use app\modules\inventory\models\Goodsreceiptdetail;
use app\modules\common\models\Unitofmeasure;

/**
 * This is the model class for table "tr_goodsreceiptdetailreturn".
 *
 * @property int $id
 * @property int $head_id
 * @property int $productid
 * @property int $unitofmeasureid
 * @property string $qty
 * @property int $storagebinid
 * @property int $goodsreceiptdetailid
 * @property string $soqty
 * @property string $giqty
 * @property string $invqty
 * 
 * @property Goodsreceiptreturn $goodsreceiptreturn
 * @property Product $product
 * @property Storagebin $storagebin
 * @property Grdetail $grdetail
 * @property Unit $unit
 */
class Goodsreceiptdetailreturn extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_goodsreceiptdetailreturn';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['head_id', 'productid', 'unitofmeasureid', 'storagebinid', 'goodsreceiptdetailid'], 'required'],
            [['head_id', 'productid', 'unitofmeasureid', 'storagebinid', 'goodsreceiptdetailid'], 'integer'],
            [['qty', 'soqty', 'giqty', 'invqty'], 'number'],
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
            'goodsreceiptdetailid' => 'ID Goods Receipt Detail',
            'soqty' => 'Jumlah Sales Order',
            'giqty' => 'Jumlah Goods Issue',
            'invqty' => 'Jumlah Invoice',
        ];
    }
    
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['productid' => 'productid']);
    }
    
    public function getGoodsreceiptreturn()
    {
        return $this->hasOne(Goodsreceiptreturn::className(), ['id' => 'head_id']);
    }
    
    public function getStoragebin()
    {
        return $this->hasOne(Storagebin::className(), ['storagebinid' => 'storagebinid']);
    }
    
    public function getGrdetail()
    {
        return $this->hasOne(Goodsreceiptdetail::className(), ['id' => 'goodsreceiptdetailid']);
    }
    
    public function getUnit()
    {
        return $this->hasOne(Unitofmeasure::className(), ['unitofmeasureid' => 'unitofmeasureid']);
    }
    
    public function afterFind() {
        parent::afterFind();
        $this->qty = round($this->qty);
    }
}
