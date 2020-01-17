<?php

namespace app\modules\accounting\models;

use Yii;
use app\modules\accounting\models\Advancepayment;
use app\modules\common\models\Customer;
use app\modules\common\models\Plant;
use app\modules\admin\models\User;

/**
 * This is the model class for table "tr_salespayment".
 *
 * @property int $id
 * @property string $sptransdate
 * @property string $sptransnum
 * @property int $plantid
 * @property int $invoicearid
 * @property int $addressbookid
 * @property string $aramount
 * @property string $paidamount
 * @property string $advanceamount
 * @property string $payamount
 * @property string $bankname
 * @property string $bankaccountno
 * @property string $receiptno
 * @property int $advancepaymentid
 * @property string $headernote
 * @property int $status
 * @property string $lockDateUntil
 * @property int $lockBy
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 * 
 * @property Advance $advance
 * @property Createdby $createdby
 * @property Updatedby $updatedby
 * @property Customer $customer
 * @property Invoice $invoicear
 * @property Plant $plant
 */
class Salespayment extends \yii\db\ActiveRecord
{
    public $stringPayamount;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_salespayment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sptransdate', 'sptransnum', 'plantid', 'invoicearid', 'addressbookid', 'createdAt', 'createdBy'], 'required', 'on' => 'create'],
            [['bankname', 'bankaccountno', 'receiptno', 'payamount', 'stringPayamount', 'invoicearid', 'addressbookid'], 'required', 'on' => 'update'],
            [['sptransdate', 'lockDateUntil', 'createdAt', 'updatedAt'], 'safe'],
            [['plantid', 'invoicearid', 'addressbookid', 'advancepaymentid', 'status', 'lockBy', 'createdBy', 'updatedBy'], 'integer'],
            [['aramount', 'paidamount', 'advanceamount', 'payamount'], 'number'],
            [['headernote'], 'string'],
            [['sptransnum', 'bankname', 'bankaccountno', 'receiptno'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sptransdate' => 'Tanggal Transaksi',
            'sptransnum' => 'Nomor Transaksi',
            'plantid' => 'Cabang',
            'invoicearid' => 'Faktur Penjualan',
            'addressbookid' => 'Pelanggan',
            'aramount' => 'Total Tagihan',
            'paidamount' => 'Sudah Dibayar',
            'advanceamount' => 'Uang Muka',
            'stringPayamount' => 'Jumlah Dibayar',
            'payamount' => 'Jumlah Dibayar',
            'bankname' => 'Nama Bank',
            'bankaccountno' => 'Nomor Rekening',
            'receiptno' => 'Nomor Bukti',
            'advancepaymentid' => 'Uang Muka Penjualan',
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
    
    public function getAdvance() {
        return $this->hasOne(Advancepayment::className(), ['id' => 'advancepaymentid']);
    }
    
    public function getCreatedby()
    {
        return $this->hasOne(User::className(), ['userID' => 'createdBy']);
    }
    
    public function getUpdatedby()
    {
        return $this->hasOne(User::className(), ['userID' => 'updatedBy']);
    }
    
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['addressbookid' => 'addressbookid']);
    }
    
    public function getInvoicear()
    {
        return $this->hasOne(Invoicear::className(), ['id' => 'invoicearid']);
    }
    
    public function getPlant()
    {
        return $this->hasOne(Plant::className(), ['plantid' => 'plantid']);
    }
    
    public function afterFind() {
        parent::afterFind();
        $this->aramount = round($this->aramount);
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
