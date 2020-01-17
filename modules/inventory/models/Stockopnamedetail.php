<?php

namespace app\modules\inventory\models;

use app\modules\common\models\Product;
use app\modules\common\models\Storagebin;
use app\modules\common\models\Unitofmeasure;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tr_stockopnamedetail".
 *
 * @property int $id
 * @property int $head_id
 * @property int $productid
 * @property int $unitofmeasureid
 * @property string $type
 * @property string $qty
 * @property int $storagebinid
 * @property string $hpp
 * @property string $total
 * 
 * @property Stockopname $stock
 * @property Product $product
 * @property Storagebin $storagebin
 * @property Unit $unit
 */
class Stockopnamedetail extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_stockopnamedetail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['head_id', 'productid', 'type', 'qty', 'unitofmeasureid', 'storagebinid', 'hpp'], 'required'],
            [['head_id', 'productid', 'unitofmeasureid', 'storagebinid'], 'integer'],
            [['qty', 'hpp', 'total'], 'number'],
            [['qty', 'hpp'], 'compare', 'compareValue' => 0, 'operator' => '>'],
            [['type'], 'string'],
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
            'type' => 'Tipe',
            'qty' => 'Jumlah',
            'storagebinid' => 'Rak',
            'hpp' => 'HPP (Rp)',
            'total' => 'Total (Rp)'
        ];
    }
    
    /**
     * @return ActiveQuery
     */
    public function getStock()
    {
        return $this->hasOne(Stockopname::className(), ['id' => 'head_id']);
    }
    
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['productid' => 'productid']);
    }
    
    public function getStoragebin()
    {
        return $this->hasOne(Storagebin::className(), ['storagebinid' => 'storagebinid']);
    }
    
    public function getUnit()
    {
        return $this->hasOne(Unitofmeasure::className(), ['unitofmeasureid' => 'unitofmeasureid']);
    }
    
    public function afterFind() {
        parent::afterFind();
        $this->qty = round($this->qty);
        $this->hpp = round($this->hpp);
    }
}
