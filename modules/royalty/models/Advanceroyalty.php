<?php

namespace app\modules\royalty\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use app\modules\common\models\Author;
use app\modules\common\models\Plant;
use app\modules\common\models\Product;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tr_advanceroyalty".
 *
 * @property int $id
 * @property string $umrtransdate
 * @property string $umrtransnum
 * @property int $plantid
 * @property int $addressbookid
 * @property int $productid
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
 * @property Author $author
 * @property Plant $plant
 * @property Product $product
 */
class Advanceroyalty extends \yii\db\ActiveRecord
{
    public $stringAmount;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_advanceroyalty';
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
            [['umrtransdate', 'umrtransnum', 'plantid', 'addressbookid', 'productid', 'createdAt', 'createdBy'], 'required', 'on' => 'create'],
            [['bankname', 'bankaccountno', 'receiptno', 'stringAmount', 'addressbookid'], 'required', 'on' => 'update'],
            [['umrtransdate', 'stringAmount', 'createdAt', 'updatedAt'], 'safe'],
            [['plantid', 'addressbookid', 'productid', 'isUsed', 'status', 'lockBy', 'createdBy', 'updatedBy'], 'integer'],
            [['amount'], 'number'],
            [['stringAmount', 'headernote'], 'string'],
            [['umrtransnum', 'bankname', 'bankaccountno', 'receiptno'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'umrtransdate' => 'Tanggal Transaksi',
            'umrtransnum' => 'Nomor Transaksi',
            'plantid' => 'Cabang',
            'addressbookid' => 'Penulis',
            'productid' => 'Buku',
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
    
    public function getAuthor()
    {
        return $this->hasOne(Author::className(), ['addressbookid' => 'addressbookid']);
    }
    
    public function getPlant()
    {
        return $this->hasOne(Plant::className(), ['plantid' => 'plantid']);
    }
    
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['productid' => 'productid']);
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
