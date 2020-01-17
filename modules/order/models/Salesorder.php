<?php

namespace app\modules\order\models;

use app\modules\accounting\models\Invoicear;
use app\modules\admin\models\User;
use app\modules\admin\models\Wfgroup;
use app\modules\common\models\Customer;
use app\modules\common\models\Paymentmethod;
use app\modules\common\models\Plant;
use app\modules\order\models\Salesorderinfo;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tr_salesorder".
 *
 * @property int $id
 * @property string $sotransdate
 * @property string $sotransnum
 * @property int $plantid
 * @property int $addressbookid
 * @property string $pocustomer
 * @property int $salestype
 * @property int $paymentmethodid
 * @property string $dueDate
 * @property string $address
 * @property string $headernote
 * @property string $totaldiscount
 * @property string $totalvat
 * @property string $grandtotal
 * @property int $status
 * @property int $isgenerated
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 * 
 * @property Customer $customer
 * @property Paymentmethod $paymentmethod
 * @property Plant $plant
 * @property Salesorderinfo $soinfo
 * @property Createdby $createdby
 * @property Updatedby $updatedby
 */
class Salesorder extends ActiveRecord
{
    CONST FEATURE_DESCRIPTION = "Menu ini digunakan untuk mencatat Pesanan Penjualan dari pelanggan.";
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_salesorder';
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
            [['sotransdate', 'plantid', 'addressbookid', 'salestype', 'paymentmethodid'], 'required'],
            [['sotransdate', 'sotransnum', 'addressbookid', 'pocustomer', 'salestype', 'paymentmethodid', 'dueDate', 'createdAt', 
                'updatedAt', 'lockDateUntil', 'lockBy', 'totaldiscount', 'totalvat', 'grandtotal', 'isgenerated'], 'safe'],
            [['plantid', 'addressbookid', 'createdBy', 'updatedBy', 'isgenerated'], 'integer'],
            [['address', 'headernote'], 'string'],
            [['sotransnum'], 'string', 'max' => 20],
            [['pocustomer'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sotransdate' => 'Tanggal Transaksi',
            'sotransnum' => 'Nomor Transaksi',
            'plantid' => 'Cabang',
            'addressbookid' => 'Pelanggan',
            'pocustomer' => 'Nomor PO Pelanggan',
            'salestype' => 'Jenis Penjualan',
            'paymentmethodid' => 'Metode Pembayaran',
            'dueDate' => 'Jatuh Tempo',
            'address' => 'Alamat Pengiriman',
            'headernote' => 'Catatan',
            'totaldiscount' => 'Total Diskon',
            'totalvat' => 'Total PPN',
            'grandtotal' => 'Grand Total (Rp)',
            'status' => 'Status',
            'isgenerated' => 'Sudah Diproses Menjadi Dokumen Lain',
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
    
    public function getPaymentmethod()
    {
        return $this->hasOne(Paymentmethod::className(), ['paymentmethodid' => 'paymentmethodid']);
    }
    
    public function getPlant()
    {
        return $this->hasOne(Plant::className(), ['plantid' => 'plantid']);
    }
    
    public function getSoinfo()
    {
        return $this->hasOne(Salesorderinfo::className(), ['salesorderid' => 'id']);
    }
    
    public function getCreatedby()
    {
        return $this->hasOne(User::className(), ['userID' => 'createdBy']);
    }
    
    public function getUpdatedby()
    {
        return $this->hasOne(User::className(), ['userID' => 'updatedBy']);
    }
    
    public static function getAdvanceSoList() {
        $maxStatus = Wfgroup::getMaxStatus('listum');
        $model1 = self::find()
            ->select(['tr_salesorder.id'])
            ->andWhere("EXISTS (
                SELECT *
                FROM tr_advancepayment 
                WHERE tr_salesorder.id = tr_advancepayment.salesorderid AND tr_advancepayment.status = $maxStatus
              )"
            )
            ->asArray()
            ->all();
        
        $model2 = Invoicear::find()
            ->innerJoinWith('goodsissue')
            ->andWhere("tr_invoicear.aramount = tr_invoicear.payamount")
            ->asArray()
            ->all();
        
        $strArray = [];
        $result = '';
        if ($model1) {
            foreach ($model1 as $data) {
                $strArray[] = $data['id'];
            }
        }
        if ($model2) {
            foreach ($model2 as $data) {
                $strArray[] = $data['goodsissue']['salesorderid'];
            }
        }

        if ($strArray) {
            foreach ($strArray as $val) {
                if ($val == end($strArray)) {
                    $result .= "'$val'";
                } else {
                    $result .= "'$val',";
                }
            }
        }
        if ($strArray) {
            return '('.$result.')';
        }
        return $result;
    }
}
