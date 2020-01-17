<?php

namespace app\modules\inventory\models;

use Yii;
use app\modules\common\models\Sloc;
use app\modules\inventory\models\Goodsissue;
use app\modules\admin\models\User;

/**
 * This is the model class for table "tr_goodsissuereturn".
 *
 * @property int $id
 * @property string $girtransdate
 * @property string $girtransnum
 * @property int $slocid
 * @property int $goodsissueid
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
 * @property Goodsissue $goodsissue
 * @property Createdby $createdby
 * @property Updatedby $updatedby
 */
class Goodsissuereturn extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_goodsissuereturn';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['girtransdate', 'girtransnum', 'slocid', 'createdAt', 'createdBy'], 'required'],
            [['girtransdate', 'lockDateUntil', 'createdAt', 'updatedAt'], 'safe'],
            [['slocid', 'goodsissueid', 'status', 'lockBy', 'createdBy', 'updatedBy'], 'integer'],
            [['headernote'], 'string'],
            [['girtransnum'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'girtransdate' => 'Tanggal Transaksi',
            'girtransnum' => 'Nomor Transaksi',
            'slocid' => 'Gudang',
            'goodsissueid' => 'Pengeluaran Barang',
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
    
    public function getGoodsissue()
    {
        return $this->hasOne(Goodsissue::className(), ['id' => 'goodsissueid']);
    }
    
    public function getSloc()
    {
        return $this->hasOne(Sloc::className(), ['slocid' => 'slocid']);
    }
}
