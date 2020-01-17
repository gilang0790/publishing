<?php

namespace app\modules\order\models;

use app\modules\common\models\Customer;
use app\modules\common\models\Product;
use app\modules\order\models\Salesorderinfo;
use Yii;

/**
 * This is the model class for table "tr_salesorderinfodetail".
 *
 * @property int $id
 * @property int $head_id
 * @property int $productid
 * @property int $unitofmeasureid
 * @property string $qty
 * @property int $addressbookid
 * @property string $soqty
 * @property string $giqty
 * @property string $invqty
 * @property string $retqty
 * 
 * @property Product $product
 * @property Soinfo $soinfo
 */
class Salesorderinfodetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_salesorderinfodetail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['head_id', 'productid', 'unitofmeasureid', 'addressbookid'], 'required'],
            [['head_id', 'productid', 'unitofmeasureid', 'addressbookid'], 'integer'],
            [['qty', 'soqty', 'giqty', 'invqty', 'retqty'], 'number'],
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
            'soqty' => 'Jumlah Penjualan',
            'giqty' => 'Jumlah Surat Pengeluaran Barang',
            'invqty' => 'Jumlah Faktur Penjualan Barang',
            'retqty' => 'Jumlah Retur',
        ];
    }
    
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['addressbookid' => 'addressbookid']);
    }
    
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['productid' => 'productid']);
    }
    
    public function getSoinfo()
    {
        return $this->hasOne(Salesorderinfo::className(), ['id' => 'head_id']);
    }
    
    public function afterFind() {
        parent::afterFind();
        $this->qty = round($this->qty);
    }
}
