<?php

namespace app\modules\purchase\models;

use Yii;
use app\modules\common\models\Product;
use app\modules\common\models\Unitofmeasure;

/**
 * This is the model class for table "tr_purchaseorderdetail".
 *
 * @property int $id
 * @property int $head_id
 * @property int $productid
 * @property int $unitofmeasureid
 * @property string $qty
 * @property string $price
 * @property string $total
 * @property string $grqty
 * @property string $invqty
 * @property string $retqty
 * 
 * @property Grdetail $grdetail
 * @property Product $product
 */
class Purchaseorderdetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_purchaseorderdetail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['head_id', 'productid', 'unitofmeasureid', 'price'], 'required'],
            [['head_id', 'productid', 'unitofmeasureid'], 'integer'],
            [['qty', 'price', 'total', 'grqty', 'invqty', 'retqty'], 'number'],
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
            'total' => 'Total (Rp)',
            'grqty' => 'Jumlah Masuk',
            'invqty' => 'Jumlah Tagihan',
            'retqty' => 'Jumlah Retur',
        ];
    }
    
//    public function getGrdetail()
//    {
//        return $this->hasOne(Goodsissuedetail::className(), ['purchaseorderdetailid' => 'id']);
//    }
    
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
    
    public static function getOutstanding() {
        $model = self::find()
            ->select(['tr_purchaseorderdetail.head_id as head_id'])
            ->where("COALESCE(tr_purchaseorderdetail.grqty, 0) < tr_purchaseorderdetail.qty")
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
