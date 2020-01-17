<?php

namespace app\modules\inventory\models;

use Yii;
use app\modules\inventory\models\Goodsissuedetail;
use app\modules\common\models\Sloc;
use app\modules\order\models\Salesorder;
use app\modules\admin\models\User;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "goodsissue".
 *
 * @property int $id
 * @property string $gitransdate
 * @property string $gitransnum
 * @property int $salesorderid
 * @property int $slocid
 * @property string $address
 * @property string $shippingname
 * @property string $shippingcost
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
 * @property GoodsissueDetails[] $transactionDetails
 * @property Sloc $sloc
 * @property Createdby $createdby
 * @property Updatedby $updatedby
 */
class Goodsissue extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_goodsissue';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gitransdate', 'salesorderid', 'slocid'], 'required'],
            [['gitransdate', 'gitransnum', 'lockDateUntil', 'createdAt', 'updatedAt', 'shippingcost'], 'safe'],
            [['salesorderid', 'slocid', 'status', 'lockBy', 'isgenerated', 'createdBy', 'updatedBy'], 'integer'],
            [['address', 'headernote'], 'string'],
            [['gitransnum'], 'string', 'max' => 20],
            [['shippingname'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gitransdate' => 'Tanggal Transaksi',
            'gitransnum' => 'Nomor Transaksi',
            'salesorderid' => 'Pesanan Penjualan',
            'slocid' => 'Gudang',
            'address' => 'Alamat Kirim',
            'shippingname' => 'Kurir',
            'shippingcost' => 'Ongkos Kirim',
            'headernote' => 'Catatan',
            'status' => 'Status',
            'isgenerated' => 'Sudah Diproses Menjadi Dokumen Lain',
            'createdAt' => 'Dibuat Pada',
            'updatedAt' => 'Diubah Pada',
            'createdBy' => 'Dibuat Oleh',
            'updatedBy' => 'Diubah Oleh',
        ];
    }
    
    public function getCreatedby()
    {
        return $this->hasOne(User::className(), ['userID' => 'createdBy']);
    }
    
    public function getUpdatedby()
    {
        return $this->hasOne(User::className(), ['userID' => 'updatedBy']);
    }
    
    public function getSalesorder()
    {
        return $this->hasOne(Salesorder::className(), ['id' => 'salesorderid']);
    }
    
    public function getSloc()
    {
        return $this->hasOne(Sloc::className(), ['slocid' => 'slocid']);
    }
    
    public function getTransactionDetails()
    {
        return $this->hasMany(Goodsissuedetail::className(), ['head_id' => 'id']);
    }
    
    public function afterFind() {
        parent::afterFind();
        $this->shippingcost = round($this->shippingcost);
    }
}
