<?php

namespace app\modules\accounting\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use app\modules\common\models\Plant;
use app\modules\order\models\Salesorder;
use app\modules\admin\models\User;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tr_advancepayment".
 *
 * @property int $id
 * @property string $umtransdate
 * @property string $umtransnum
 * @property int $plantid
 * @property int $salesorderid
 * @property string $amount
 * @property string $bankname
 * @property string $bankaccountno
 * @property string $receiptno
 * @property int $isUsed
 * @property string $headernote
 * @property int $status
 * @property string $lockDateUntil
 * @property int $lockBy
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 * 
 * @property Createdby $createdby
 * @property Updatedby $updatedby
 * @property Salesorder $salesorder
 * @property Plant $plant
 */
class Advancepayment extends \yii\db\ActiveRecord
{
    public $stringAmount;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_advancepayment';
    }
    
    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'createdAt',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updatedAt',
                ],
                'value' => function() {
                    return date('Y-m-d H:i:s');
                }
            ],
            [
                'class' => BlameableBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['createdBy'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updatedBy'],
                ],
                'value' => function() {
                    return (Yii::$app->user->isGuest) ? 1 : Yii::$app->user->identity->userID;
                }
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['umtransdate', 'umtransnum', 'salesorderid', 'stringAmount', 'createdAt', 'createdBy'], 'required', 'on' => 'create'],
            [['bankname', 'bankaccountno', 'receiptno', 'stringAmount', 'salesorderid'], 'required', 'on' => 'update'],
            [['umtransdate', 'plantid', 'lockDateUntil', 'createdAt', 'updatedAt'], 'safe'],
            [['plantid', 'salesorderid', 'isUsed', 'status', 'lockBy', 'createdBy', 'updatedBy'], 'integer'],
            [['amount'], 'number'],
            [['stringAmount', 'headernote'], 'string'],
            [['umtransnum', 'bankname', 'bankaccountno', 'receiptno'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'umtransdate' => 'Tanggal Transaksi',
            'umtransnum' => 'Nomor Transaksi',
            'plantid' => 'Cabang',
            'salesorderid' => 'Pesanan Penjualan',
            'stringAmount' => 'Jumlah Dibayar',
            'amount' => 'Jumlah Dibayar',
            'bankname' => 'Nama Bank',
            'bankaccountno' => 'Nomor Rekening',
            'receiptno' => 'Nomor Bukti',
            'isUsed' => 'Sudah Digunakan?',
            'headernote' => 'Catatan',
            'status' => 'Status',
            'lockDateUntil' => 'Lock Date Until',
            'lockBy' => 'Lock By',
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
    
    public function getPlant()
    {
        return $this->hasOne(Plant::className(), ['plantid' => 'plantid']);
    }
    
    public function afterFind() {
        parent::afterFind();
        $this->amount = round($this->amount);
        $this->stringAmount = strval($this->amount);
    }
    
    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        
        $stringAmount = str_replace(".", "", $this->stringAmount);
        $this->amount = (float) $stringAmount;
        return true;
    }
}
