<?php

namespace app\modules\purchase\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use app\modules\common\models\Supplier;
use app\modules\common\models\Plant;
use app\modules\common\models\Paymentmethod;
use app\modules\purchase\models\Purchaseorderdetail;
use app\modules\admin\models\User;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tr_purchaseorder".
 *
 * @property int $id
 * @property string $potransdate
 * @property string $potransnum
 * @property int $plantid
 * @property int $addressbookid
 * @property int $paymentmethodid
 * @property string $billto
 * @property string $shipto
 * @property string $headernote
 * @property string $grandtotal
 * @property int $status
 * @property int $isgenerated
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 * 
 * @property Supplier $supplier
 * @property Plant $plant
 * @property Paymentmethod $paymentmethod
 * @property PurchaseorderDetails[] $transactionDetails
 * @property Createdby $createdby
 * @property Updatedby $updatedby
 */
class Purchaseorder extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_purchaseorder';
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
            [['potransdate', 'plantid', 'addressbookid', 'paymentmethodid'], 'required'],
            [['potransdate', 'potransnum', 'addressbookid', 'paymentmethodid', 'billto', 'createdAt', 
                'updatedAt', 'lockDateUntil', 'lockBy', 'shipto', 'grandtotal', 'isgenerated'], 'safe'],
            [['plantid', 'addressbookid', 'createdBy', 'updatedBy', 'isgenerated'], 'integer'],
            [['shipto', 'billto', 'headernote'], 'string'],
            [['potransnum'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'potransdate' => 'Tanggal Transaksi',
            'potransnum' => 'Nomor Transaksi',
            'plantid' => 'Cabang',
            'addressbookid' => 'Pemasok',
            'paymentmethodid' => 'Cara Pembayaran',
            'billto' => 'Alamat Penagihan',
            'shipto' => 'Alamat Pengiriman',
            'headernote' => 'Catatan',
            'grandtotal' => 'Grand Total (Rp)',
            'status' => 'Status',
            'isgenerated' => 'Sudah Diproses Menjadi Dokumen Lain',
            'createdAt' => 'Dibuat Pada',
            'updatedAt' => 'Diubah Pada',
            'createdBy' => 'Dibuat Oleh',
            'updatedBy' => 'Diubah Oleh',
        ];
    }
    
    public function getSupplier()
    {
        return $this->hasOne(Supplier::className(), ['addressbookid' => 'addressbookid']);
    }
    
    public function getPaymentmethod()
    {
        return $this->hasOne(Paymentmethod::className(), ['paymentmethodid' => 'paymentmethodid']);
    }
    
    public function getPlant()
    {
        return $this->hasOne(Plant::className(), ['plantid' => 'plantid']);
    }
    
    public function getTransactionDetails()
    {
        return $this->hasMany(Purchaseorderdetail::className(), ['head_id' => 'id']);
    }
    
    public function getCreatedby()
    {
        return $this->hasOne(User::className(), ['userID' => 'createdBy']);
    }
    
    public function getUpdatedby()
    {
        return $this->hasOne(User::className(), ['userID' => 'updatedBy']);
    }
}
