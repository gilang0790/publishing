<?php

namespace app\modules\inventory\models;

use Yii;
use app\modules\purchase\models\Purchaseorder;
use app\modules\common\models\Sloc;
use app\modules\admin\models\User;

/**
 * This is the model class for table "tr_goodsreceipt".
 *
 * @property int $id
 * @property string $grtransdate
 * @property string $grtransnum
 * @property int $purchaseorderid
 * @property int $slocid
 * @property string $headernote
 * @property string $image
 * @property int $status
 * @property string $lockDateUntil
 * @property int $lockBy
 * @property int $isgenerated
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 * 
 * @property GoodsreceiptDetails[] $transactionDetails
 * @property Sloc $sloc
 * @property Createdby $createdby
 * @property Updatedby $updatedby
 */
class Goodsreceipt extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_goodsreceipt';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['grtransdate', 'grtransnum', 'purchaseorderid', 'createdAt', 'createdBy'], 'required'],
            [['grtransdate', 'lockDateUntil', 'createdAt', 'updatedAt'], 'safe'],
            [['purchaseorderid', 'slocid', 'status', 'lockBy', 'isgenerated', 'createdBy', 'updatedBy'], 'integer'],
            [['headernote', 'image'], 'string'],
            [['grtransnum'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'grtransdate' => 'Tanggal Transaksi',
            'grtransnum' => 'Nomor Transaksi',
            'purchaseorderid' => 'Nomor Pembelian',
            'slocid' => 'Gudang',
            'headernote' => 'Catatan',
            'image' => 'Gambar',
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
    
    public function getPurchaseorder()
    {
        return $this->hasOne(Purchaseorder::className(), ['id' => 'purchaseorderid']);
    }
    
    public function getSloc()
    {
        return $this->hasOne(Sloc::className(), ['slocid' => 'slocid']);
    }
    
    public function getTransactionDetails()
    {
        return $this->hasMany(Goodsreceiptdetail::className(), ['head_id' => 'id']);
    }
}
