<?php

namespace app\modules\inventory\models;

use Yii;
use app\modules\common\models\Product;
use app\modules\common\models\Sloc;
use app\modules\common\models\Storagebin;
use app\modules\common\models\Unitofmeasure;

/**
 * This is the model class for table "stockcard".
 *
 * @property int $stockcardid
 * @property int $stockid
 * @property int $productid
 * @property int $unitofmeasureid
 * @property int $slocid
 * @property int $storagebinid
 * @property string $transdate
 * @property string $refnum
 * @property string $qtyin
 * @property string $qtyout
 * @property string $transtype
 * @property string $hpp
 * @property string $buyprice
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 * 
 * @property Product $product
 * @property Sloc $sloc
 * @property Stock $stock
 * @property Storagebin $storagebin
 * @property Unitofmeasure $uom
 */
class Stockcard extends \yii\db\ActiveRecord
{
    public $categoryid;
    public $categoryname;
    public $plantid;
    public $plantcode;
    public $productcode;
    public $productname;
    public $sloccode;
    public $stockQty;
    public $stockValue;
    public $uomcode;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stockcard';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stockid', 'productid', 'unitofmeasureid', 'slocid', 'storagebinid', 'transdate', 'refnum', 'transtype', 'createdAt', 'createdBy'], 'required'],
            [['stockid', 'productid', 'unitofmeasureid', 'slocid', 'storagebinid', 'createdBy', 'updatedBy'], 'integer'],
            [['transdate', 'createdAt', 'updatedAt'], 'safe'],
            [['qtyin', 'qtyout', 'hpp', 'buyprice'], 'number'],
            [['refnum', 'transtype'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'stockcardid' => 'ID',
            'stockid' => 'ID Stok',
            'productid' => 'Barang',
            'unitofmeasureid' => 'Satuan',
            'slocid' => 'Gudang',
            'storagebinid' => 'Rak',
            'transdate' => 'Tanggal Transaksi',
            'refnum' => 'Nomor Referensi',
            'qtyin' => 'Masuk',
            'qtyout' => 'Keluar',
            'transtype' => 'Tipe Transaksi',
            'hpp' => 'HPP',
            'buyprice' => 'Harga Beli',
            'createdAt' => 'Dibuat Pada',
            'updatedAt' => 'Diubah Pada',
            'createdBy' => 'Dibuat Oleh',
            'updatedBy' => 'Diubah Oleh',
            'plantid' => 'Cabang',
            'plantcode' => 'Cabang',
            'sloccode' => 'Gudang',
            'categoryid' => 'Kategori',
            'categoryname' => 'Kategori',
            'productname' => 'Nama Barang',
            'productcode' => 'Kode',
            'uomcode' => 'Satuan',
            'stockQty' => 'Jumlah',
            'stockValue' => 'Nilai'
        ];
    }
    
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['productid' => 'productid']);
    }
    
    public function getSloc()
    {
        return $this->hasOne(Sloc::className(), ['slocid' => 'slocid']);
    }
    
    public function getStock()
    {
        return $this->hasOne(Stock::className(), ['stockid' => 'stockid']);
    }
    
    public function getStoragebin()
    {
        return $this->hasOne(Storagebin::className(), ['storagebinid' => 'storagebinid']);
    }
    
    public function getUom()
    {
        return $this->hasOne(Unitofmeasure::className(), ['unitofmeasureid' => 'unitofmeasureid']);
    }
}
