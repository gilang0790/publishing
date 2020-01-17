<?php

namespace app\modules\inventory\models;

use Yii;

/**
 * This is the model class for table "stock".
 *
 * @property int $stockid
 * @property int $productid
 * @property int $unitofmeasureid
 * @property int $slocid
 * @property int $storagebinid
 * @property string $qty
 * @property string $hpp
 * @property string $buyprice
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 */
class Stock extends \yii\db\ActiveRecord
{
    public $categoryid;
    public $categoryname;
    public $plantid;
    public $plantcode;
    public $productcode;
    public $productname;
    public $sloccode;
    public $uomcode;
    public $stockValue;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stock';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['productid', 'storagebinid', 'createdBy'], 'required'],
            [['productid', 'unitofmeasureid', 'slocid', 'storagebinid', 'createdBy', 'updatedBy'], 'integer'],
            [['qty', 'hpp', 'buyprice'], 'number'],
            [['createdAt', 'updatedAt'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'stockid' => 'ID',
            'productid' => 'ID Barang',
            'unitofmeasureid' => 'ID Satuan',
            'slocid' => 'Gudang',
            'storagebinid' => 'ID Rak',
            'qty' => 'Jumlah',
            'hpp' => 'Nilai',
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
            'stockValue' => 'Nilai'
        ];
    }
    
    public static function getStock($slocid, $storagebinid, $productid) {
        $result = 0;
        $model = self::find()->where(['slocid' => $slocid, 'storagebinid' => $storagebinid, 'productid' => $productid]);
        $model->andWhere('qty > 0');
        $queryOne = $model->one();
        if ($queryOne) {
            $result = $queryOne->qty;
        }
        return $result;
    }
}
