<?php

namespace app\modules\inventory\models;

use Yii;
use app\modules\common\models\Sloc;
use app\modules\inventory\models\Goodsreceipt;
use app\modules\admin\models\User;

/**
 * This is the model class for table "tr_goodsreceiptreturn".
 *
 * @property int $id
 * @property string $grrtransdate
 * @property string $grrtransnum
 * @property int $slocid
 * @property int $goodsreceiptid
 * @property string $headernote
 * @property int $status
 * @property string $lockDateUntil
 * @property int $lockBy
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 * 
 * @property Sloc $sloc
 * @property Goodsreceipt $goodsreceipt
 * @property Createdby $createdby
 * @property Updatedby $updatedby
 */
class Goodsreceiptreturn extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_goodsreceiptreturn';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['grrtransdate', 'grrtransnum', 'slocid', 'createdAt', 'createdBy'], 'required'],
            [['grrtransdate', 'lockDateUntil', 'createdAt', 'updatedAt'], 'safe'],
            [['slocid', 'goodsreceiptid', 'status', 'lockBy', 'createdBy', 'updatedBy'], 'integer'],
            [['headernote'], 'string'],
            [['grrtransnum'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'grrtransdate' => 'Tanggal Transaksi',
            'grrtransnum' => 'Nomor Transaksi',
            'slocid' => 'Gudang',
            'goodsreceiptid' => 'Penerimaan Barang',
            'headernote' => 'Catatan',
            'status' => 'Status',
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
    
    public function getGoodsreceipt()
    {
        return $this->hasOne(Goodsreceipt::className(), ['id' => 'goodsreceiptid']);
    }
    
    public function getSloc()
    {
        return $this->hasOne(Sloc::className(), ['slocid' => 'slocid']);
    }
}
