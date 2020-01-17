<?php

namespace app\modules\accounting\models;

use app\modules\admin\models\User;
use app\modules\common\models\Customer;
use app\modules\common\models\Paymentmethod;
use app\modules\common\models\Plant;
use app\modules\inventory\models\Goodsissue;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tr_invoicear".
 *
 * @property int $id
 * @property string $artransdate
 * @property string $artransnum
 * @property int $plantid
 * @property int $addressbookid
 * @property int $goodsissueid
 * @property int $paymentmethodid
 * @property string $dueDate
 * @property string $shippingcost
 * @property string $grandtotal
 * @property string $aramount
 * @property string $payamount
 * @property string $headernote
 * @property int $status
 * @property string $lockDateUntil
 * @property int $lockBy
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 * 
 * @property Customer $customer
 * @property Goodsissue $goodsissue
 * @property Paymentmethod $paymentmethod
 * @property Plant $plant
 * @property Createdby $createdby
 * @property Updatedby $updatedby
 */
class Invoicear extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_invoicear';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['artransdate', 'artransnum', 'plantid', 'goodsissueid', 'createdAt', 'createdBy'], 'required', 'on' => 'create'],
            [['artransdate', 'artransnum', 'plantid', 'goodsissueid', 'paymentmethodid', 'dueDate', 'aramount', 'payamount', 'lockDateUntil', 
                'createdAt', 'createdBy', 'updatedAt', 'updatedBy'], 'safe'],
            [['plantid', 'goodsissueid', 'status', 'lockBy', 'createdBy', 'updatedBy'], 'integer'],
            [['grandtotal', 'shippingcost', 'aramount', 'payamount'], 'number'],
            [['headernote'], 'string'],
            [['artransnum'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'artransdate' => 'Tanggal Transaksi',
            'artransnum' => 'Nomor Transaksi',
            'plantid' => 'Cabang',
            'goodsissueid' => 'Pengeluaran Barang',
            'addressbookid' => 'Pelanggan',
            'paymentmethodid' => 'Metode Pembayaran',
            'dueDate' => 'Jatuh Tempo',
            'shippingcost' => 'Ongkos Kirim',
            'grandtotal' => 'Total Nilai Barang',
            'aramount' => 'Jumlah Tagihan',
            'payamount' => 'Jumlah Dibayar',
            'headernote' => 'Catatan',
            'status' => 'Status',
            'createdAt' => 'Dibuat Pada',
            'updatedAt' => 'Diubah Pada',
            'createdBy' => 'Dibuat Oleh',
            'updatedBy' => 'Diubah Oleh',
        ];
    }
    
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['addressbookid' => 'addressbookid']);
    }
    
    public function getGoodsissue()
    {
        return $this->hasOne(Goodsissue::className(), ['id' => 'goodsissueid']);
    }
    
    public function getPaymentmethod()
    {
        return $this->hasOne(Paymentmethod::className(), ['paymentmethodid' => 'paymentmethodid']);
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
        $this->shippingcost = round($this->shippingcost);
        $this->grandtotal = round($this->grandtotal);
        $this->aramount = round($this->aramount);
        $this->payamount = round($this->payamount);
    }
}
