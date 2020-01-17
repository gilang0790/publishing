<?php

namespace app\modules\accounting\models;

use Yii;
use app\modules\common\models\Customer;
use app\modules\inventory\models\Goodsissuedetail;
use app\modules\common\models\Product;
use app\modules\common\models\Unitofmeasure;

/**
 * This is the model class for table "tr_invoiceardetail".
 *
 * @property int $id
 * @property int $head_id
 * @property int $productid
 * @property int $unitofmeasureid
 * @property string $qty
 * @property int $addressbookid
 * @property string $price
 * @property string $discount
 * @property string $totaldiscount
 * @property string $vat
 * @property string $totalvat
 * @property string $total
 * @property int $goodsissuedetailid
 * @property int $soqty
 * @property int $giqty
 * @property int $payqty
 * @property int $retqty
 * 
 * @property Customer $customer
 * @property Product $product
 * @property Unit $unit
 */
class Invoiceardetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_invoiceardetail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['head_id', 'productid', 'unitofmeasureid'], 'required'],
            [['head_id', 'productid', 'unitofmeasureid', 'goodsissuedetailid', 'soqty', 'giqty', 'payqty', 'retqty'], 'integer'],
            [['qty', 'price', 'discount', 'totaldiscount', 'vat', 'totalvat', 'total'], 'number'],
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
            'addressbookid' => 'Toko',
            'price' => 'Harga (Rp)',
            'discount' => 'Diskon (%)',
            'totaldiscount' => 'Total Diskon',
            'vat' => 'PPN (%)',
            'totalvat' => 'Total PPN',
            'total' => 'Total',
            'goodsissuedetailid' => 'ID Pengeluaran Barang Detail',
            'soqty' => 'Jumlah Sales Order',
            'giqty' => 'Jumlah Dikeluarkan',
            'payqty' => 'Jumlah Dibayar',
            'retqty' => 'Jumlah Retur',
        ];
    }
    
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['addressbookid' => 'addressbookid']);
    }
    
    public function getGidetail()
    {
        return $this->hasOne(Goodsissuedetail::className(), ['id' => 'goodsissuedetailid']);
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
        $this->vat = round($this->vat);
        $this->discount = round($this->discount);
        $this->total = round($this->total);
    }
}
