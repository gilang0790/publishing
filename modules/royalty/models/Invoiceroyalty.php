<?php

namespace app\modules\royalty\models;

use Yii;
use app\modules\common\models\Author;
use app\modules\common\models\Plant;
use app\modules\common\models\Product;

/**
 * This is the model class for table "tr_invoiceroyalty".
 *
 * @property int $id
 * @property string $transdate
 * @property string $transnum
 * @property int $plantid
 * @property int $royaltysettingid
 * @property int $addressbookid
 * @property int $productid
 * @property string $totalqty
 * @property string $amount
 * @property string $payamount
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 * 
 * @property Author $author
 * @property Plant $plant
 * @property Product $product
 */
class Invoiceroyalty extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_invoiceroyalty';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['transdate', 'transnum', 'createdAt', 'updatedAt'], 'safe'],
            [['plantid', 'royaltysettingid', 'addressbookid', 'productid', 'createdBy', 'updatedBy'], 'integer'],
            [['totalqty', 'amount', 'payamount'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'transdate' => 'Periode',
            'transnum' => 'Nomor Transaksi',
            'plantid' => 'Cabang',
            'royaltysettingid' => 'Pengaturan',
            'addressbookid' => 'Penulis',
            'productid' => 'Buku',
            'totalqty' => 'Kuantitas',
            'amount' => 'Nilai Royalti',
            'payamount' => 'Nilai Dibayar',
            'createdAt' => 'Dibuat Pada',
            'updatedAt' => 'Diubah Pada',
            'createdBy' => 'Dibuat Oleh',
            'updatedBy' => 'Diubah Oleh',
        ];
    }
    
    public function getAuthor()
    {
        return $this->hasOne(Author::className(), ['addressbookid' => 'addressbookid']);
    }
    
    public function getPlant()
    {
        return $this->hasOne(Plant::className(), ['plantid' => 'plantid']);
    }
    
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['productid' => 'productid']);
    }
}
