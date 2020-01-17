<?php

namespace app\modules\accounting\models;

use Yii;
use app\modules\common\models\Product;
use app\modules\common\models\Unitofmeasure;

/**
 * This is the model class for table "tr_invoiceapdetail".
 *
 * @property int $id
 * @property int $head_id
 * @property int $productid
 * @property int $unitofmeasureid
 * @property string $qty
 * @property string $price
 * @property string $total
 * @property int $goodsreceiptdetailid
 * @property string $poqty
 * @property string $grqty
 * @property string $retqty
 * 
 * @property Unit $unit
 */
class Invoiceapdetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_invoiceapdetail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['head_id'], 'required'],
            [['head_id', 'productid', 'unitofmeasureid', 'goodsreceiptdetailid'], 'integer'],
            [['qty', 'price', 'poqty', 'grqty', 'retqty', 'total'], 'number'],
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
            'price' => 'Harga (Rp)',
            'total' => 'Total',
            'goodsreceiptdetailid' => 'ID Penerimaan Barang Detail',
            'poqty' => 'Jumlah Pesanan Pembelian',
            'grqty' => 'Jumlah Penerimaan Barang',
            'retqty' => 'Jumlah Retur Barang',
        ];
    }
    
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['productid' => 'productid']);
    }
    
    public function getUnit()
    {
        return $this->hasOne(Unitofmeasure::className(), ['unitofmeasureid' => 'unitofmeasureid']);
    }
    
    public function afterFind() {
        parent::afterFind();
        $this->qty = round($this->qty);
        $this->price = round($this->price);
        $this->total = round($this->total);
    }
}
