<?php

namespace app\modules\accounting\models;

use Yii;
use app\modules\inventory\models\Goodsreceipt;
use app\modules\common\models\Plant;
use app\modules\common\models\Supplier;
use app\modules\admin\models\User;

/**
 * This is the model class for table "tr_invoiceap".
 *
 * @property int $id
 * @property string $aptransdate
 * @property string $aptransnum
 * @property int $plantid
 * @property int $goodsreceiptid
 * @property int $addressbookid
 * @property string $apamount
 * @property string $payamount
 * @property string $headernote
 * @property int $status
 * @property string $lockDateUntil
 * @property int $lockBy
 * @property int $isgenerated
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 * 
 * @property Supplier $supplier
 * @property Goodsreceipt $goodsreceipt
 * @property Plant $plant
 * @property Createdby $createdby
 * @property Updatedby $updatedby
 */
class Invoiceap extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_invoiceap';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['aptransdate', 'aptransnum', 'plantid', 'goodsreceiptid', 'createdAt'], 'required'],
            [['aptransdate', 'lockDateUntil', 'createdAt', 'updatedAt'], 'safe'],
            [['plantid', 'goodsreceiptid', 'addressbookid', 'status', 'lockBy', 'isgenerated', 'createdBy', 'updatedBy'], 'integer'],
            [['apamount', 'payamount'], 'number'],
            [['headernote'], 'string'],
            [['aptransnum'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'aptransdate' => 'Tanggal Transaksi',
            'aptransnum' => 'Nomor Transaksi',
            'plantid' => 'Cabang',
            'goodsreceiptid' => 'Penerimaan Barang',
            'addressbookid' => 'Pemasok',
            'apamount' => 'Jumlah Tagihan',
            'payamount' => 'Jumlah Dibayar',
            'headernote' => 'Catatan',
            'status' => 'Status',
            'createdAt' => 'Dibuat Pada',
            'updatedAt' => 'Diubah Pada',
            'createdBy' => 'Dibuat Oleh',
            'updatedBy' => 'Diubah Oleh',
        ];
    }
    
    public function getSupplier()
    {
        return $this->hasOne(Supplier::className(), ['addressbookid' => 'addressbookid']);
    }
    
    public function getGoodsreceipt()
    {
        return $this->hasOne(Goodsreceipt::className(), ['id' => 'goodsreceiptid']);
    }
    
    public function getPlant()
    {
        return $this->hasOne(Plant::className(), ['plantid' => 'plantid']);
    }
    
    public function getCreatedby()
    {
        return $this->hasOne(User::className(), ['userID' => 'createdBy']);
    }
    
    public function getUpdatedby()
    {
        return $this->hasOne(User::className(), ['userID' => 'updatedBy']);
    }
    
    public function afterFind() {
        parent::afterFind();
        $this->apamount = round($this->apamount);
        $this->payamount = round($this->payamount);
    }
}
