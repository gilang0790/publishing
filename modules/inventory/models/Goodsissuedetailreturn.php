<?php

namespace app\modules\inventory\models;

use Yii;
use app\modules\inventory\models\Goodsissuereturn;
use app\modules\common\models\Product;
use app\modules\common\models\Storagebin;
use app\modules\inventory\models\Goodsissuedetail;
use app\modules\common\models\Unitofmeasure;

/**
 * This is the model class for table "tr_goodsissuedetailreturn".
 *
 * @property int $id
 * @property int $head_id
 * @property int $productid
 * @property int $unitofmeasureid
 * @property string $qty
 * @property int $storagebinid
 * @property int $goodsissuedetailid
 * @property string $soqty
 * @property string $giqty
 * @property string $invqty
 * 
 * @property Goodsissuereturn $goodsissuereturn
 * @property Product $product
 * @property Storagebin $storagebin
 * @property Gidetail $gidetail
 * @property Unit $unit
 */
class Goodsissuedetailreturn extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_goodsissuedetailreturn';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['head_id', 'productid', 'unitofmeasureid', 'storagebinid', 'goodsissuedetailid'], 'required'],
            [['head_id', 'productid', 'unitofmeasureid', 'storagebinid', 'goodsissuedetailid'], 'integer'],
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
            'goodsissuedetailid' => 'ID Goods Issue Detail',
            'soqty' => 'Jumlah Sales Order',
            'giqty' => 'Jumlah Goods Issue',
            'invqty' => 'Jumlah Invoice',
        ];
    }
    
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['productid' => 'productid']);
    }
    
    public function getGoodsissuereturn()
    {
        return $this->hasOne(Goodsissuereturn::className(), ['id' => 'head_id']);
    }
    
    public function getStoragebin()
    {
        return $this->hasOne(Storagebin::className(), ['storagebinid' => 'storagebinid']);
    }
    
    public function getGidetail()
    {
        return $this->hasOne(Goodsissuedetail::className(), ['id' => 'goodsissuedetailid']);
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
