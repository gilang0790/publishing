<?php

namespace app\modules\order\models;

use Yii;
use app\modules\inventory\models\Goodsissuedetail;
use app\modules\common\models\Product;
use app\modules\common\models\Unitofmeasure;

/**
 * This is the model class for table "tr_salesorderdetail".
 *
 * @property int $id
 * @property int $head_id
 * @property int $productid
 * @property int $unitofmeasureid
 * @property string $qty
 * @property string $price
 * @property string $discount
 * @property string $totaldiscount
 * @property string $vat
 * @property string $totalvat
 * @property string $total
 * @property string $giqty
 * @property string $invqty
 * @property string $retqty
 * 
 * @property Gidetail $gidetail
 * @property Product $product
 * @property Unit $unit
 */
class Salesorderdetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_salesorderdetail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['head_id', 'productid', 'unitofmeasureid', 'price'], 'required'],
            [['head_id', 'productid', 'unitofmeasureid'], 'integer'],
            [['qty', 'price', 'discount', 'totaldiscount', 'vat', 'totalvat', 'total', 'giqty', 'invqty', 'retqty'], 'number'],
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
            'discount' => 'Diskon (%)',
            'totaldiscount' => 'Total Diskon',
            'vat' => 'PPN (%)',
            'totalvat' => 'Total PPN',
            'total' => 'Total (Rp)',
            'giqty' => 'Jumlah Dikeluarkan',
            'invqty' => 'Jumlah Tagihan',
            'retqty' => 'Jumlah Retur',
        ];
    }
    
    public function getGidetail()
    {
        return $this->hasOne(Goodsissuedetail::className(), ['salesorderdetailid' => 'id']);
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
    
    public static function getProductList($salesorderid) {
        $model = self::find()
            ->select(['tr_salesorderdetail.productid as product'])
            ->andWhere(['=', 'tr_salesorderdetail.head_id', $salesorderid])
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
}
