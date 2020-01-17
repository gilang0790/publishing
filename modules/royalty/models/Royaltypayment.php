<?php

namespace app\modules\royalty\models;

use Yii;
use app\modules\common\models\Plant;

/**
 * This is the model class for table "tr_royaltypayment".
 *
 * @property int $id
 * @property string $rptransdate
 * @property string $rptransnum
 * @property int $plantid
 * @property int $invoiceroyaltyid
 * @property int $advanceroyaltyid
 * @property string $invoiceamount
 * @property string $paidamount
 * @property string $advanceamount
 * @property string $payamount
 * @property string $bankname
 * @property string $bankaccountno
 * @property string $receiptno
 * @property string $headernote
 * @property int $status
 * @property string $lockDateUntil
 * @property int $lockBy
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 * 
 * @property Advanceroyalty $advanceroyalty
 * @property Invoiceroyalty $invoiceroyalty
 * @property Plant $plant
 */
class Royaltypayment extends \yii\db\ActiveRecord
{
    public $stringPayamount;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_royaltypayment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rptransdate', 'rptransnum', 'invoiceroyaltyid', 'invoiceamount', 'createdAt', 'createdBy'], 'required', 'on' => 'create'],
            [['rptransdate', 'rptransnum', 'stringPayamount'], 'required', 'on' => 'update'],
            [['rptransdate', 'stringPayamount', 'lockDateUntil', 'createdAt', 'updatedAt'], 'safe'],
            [['plantid', 'invoiceroyaltyid', 'advanceroyaltyid', 'status', 'lockBy', 'createdBy', 'updatedBy'], 'integer'],
            [['invoiceamount', 'paidamount', 'advanceamount', 'payamount'], 'number'],
            [['headernote'], 'string'],
            [['rptransnum', 'bankname'], 'string', 'max' => 20],
            [['bankaccountno', 'receiptno'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rptransdate' => 'Tanggal Transaksi',
            'rptransnum' => 'Nomor Transaksi',
            'plantid' => 'Cabang',
            'invoiceroyaltyid' => 'Nomor Invoice',
            'advanceroyaltyid' => 'Nomor Uang Muka',
            'invoiceamount' => 'Jumlah Tagihan',
            'paidamount' => 'Jumlah Terbayar',
            'advanceamount' => 'Jumlah Uang Muka',
            'stringPayamount' => 'Jumlah Dibayar',
            'payamount' => 'Jumlah Dibayar',
            'bankname' => 'Nama Bank',
            'bankaccountno' => 'Nomor Rekening',
            'receiptno' => 'Nomor Bukti',
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
    
    public function getAdvanceroyalty()
    {
        return $this->hasOne(Advanceroyalty::className(), ['id' => 'advanceroyaltyid']);
    }
    
    public function getInvoiceroyalty()
    {
        return $this->hasOne(Invoiceroyalty::className(), ['id' => 'invoiceroyaltyid']);
    }
    
    public function getPlant()
    {
        return $this->hasOne(Plant::className(), ['plantid' => 'plantid']);
    }
    
    public function afterFind() {
        parent::afterFind();
        $this->invoiceamount = round($this->invoiceamount);
        $this->paidamount = round($this->paidamount);
        $this->advanceamount = round($this->advanceamount);
        $this->payamount = round($this->payamount);
        $this->stringPayamount = strval($this->payamount);
    }

    public function beforeSave($insert) {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        
        $stringPayamount = str_replace(".", "", $this->stringPayamount);
        $this->payamount = (float) $stringPayamount;
        return true;
    }
}
