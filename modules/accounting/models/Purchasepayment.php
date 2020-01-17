<?php

namespace app\modules\accounting\models;

use Yii;
use app\modules\common\models\Supplier;
use app\modules\common\models\Plant;
use app\modules\admin\models\User;

/**
 * This is the model class for table "tr_purchasepayment".
 *
 * @property int $id
 * @property string $pptransdate
 * @property string $pptransnum
 * @property int $plantid
 * @property int $invoiceapid
 * @property int $addressbookid
 * @property string $payamount
 * @property string $apamount
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
 * @property Supplier $supplier
 * @property Invoice $invoiceap
 * @property Plant $plant
 * @property Createdby $createdby
 * @property Updatedby $updatedby
 */
class Purchasepayment extends \yii\db\ActiveRecord
{
    public $stringPayamount;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_purchasepayment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pptransdate', 'pptransnum', 'plantid', 'invoiceapid', 'addressbookid', 'createdAt', 'createdBy'], 'required', 'on' => 'create'],
            [['bankname', 'bankaccountno', 'receiptno', 'payamount', 'stringPayamount', 'invoiceapid', 'addressbookid'], 'required', 'on' => 'update'],
            [['pptransdate', 'lockDateUntil', 'createdAt', 'updatedAt'], 'safe'],
            [['plantid', 'invoiceapid', 'addressbookid', 'status', 'lockBy', 'createdBy', 'updatedBy'], 'integer'],
            [['payamount', 'apamount'], 'number'],
            [['headernote'], 'string'],
            [['pptransnum', 'bankname', 'bankaccountno', 'receiptno'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pptransdate' => 'Tanggal Transaksi',
            'pptransnum' => 'Nomor Transaksi',
            'plantid' => 'Cabang',
            'invoiceapid' => 'Faktur Penjualan',
            'addressbookid' => 'Pemasok',
            'payamount' => 'Jumlah Dibayar',
            'stringPayamount' => 'Jumlah Dibayar',
            'apamount' => 'Sisa Tagihan',
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
    
    public function getCreatedby()
    {
        return $this->hasOne(User::className(), ['userID' => 'createdBy']);
    }
    
    public function getUpdatedby()
    {
        return $this->hasOne(User::className(), ['userID' => 'updatedBy']);
    }
    
    public function getSupplier()
    {
        return $this->hasOne(Supplier::className(), ['addressbookid' => 'addressbookid']);
    }
    
    public function getInvoiceap()
    {
        return $this->hasOne(Invoiceap::className(), ['id' => 'invoiceapid']);
    }
    
    public function getPlant()
    {
        return $this->hasOne(Plant::className(), ['plantid' => 'plantid']);
    }
    
    public function afterFind() {
        parent::afterFind();
        $this->apamount = round($this->apamount);
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
