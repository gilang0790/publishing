<?php

namespace app\modules\common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use app\modules\admin\models\City;

/**
 * This is the model class for table "ms_addressbook".
 *
 * @property int $addressbookid
 * @property string $fullname
 * @property string $pic
 * @property int $isauthor
 * @property int $iscustomer
 * @property int $isemployee
 * @property int $isvendor
 * @property int $ishospital
 * @property string $publishercode
 * @property string $bankname
 * @property string $bankaccountno
 * @property string $address
 * @property int $cityid
 * @property string $email
 * @property string $phoneno
 * @property int $discount
 * @property int $status
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 *
 * @property City $city
 * @property Product[] $msProducts
 */
class Author extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ms_addressbook';
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
            [['fullname', 'email', 'phoneno', 'cityid'], 'required', 'on' => 'create'],
            [['fullname', 'email', 'phoneno', 'cityid'], 'required', 'on' => 'update'],
            [['isauthor', 'iscustomer', 'isemployee', 'isvendor', 'ishospital', 'cityid', 'discount', 'status'], 'integer'],
            [['fullname', 'bankname', 'bankaccountno', 'phoneno'], 'string', 'max' => 50],
            [['pic', 'email'], 'string', 'max' => 100],
            [['publishercode'], 'string', 'max' => 45],
            [['address'], 'string', 'max' => 250],
            [['createdAt', 'updatedAt', 'createdBy', 'updatedBy'], 'safe'],
            [['cityid'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['cityid' => 'cityid']],
            [['isauthor', 'iscustomer', 'isemployee', 'isvendor', 'ishospital'], 'default', 'value' => 0],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'addressbookid' => 'ID',
            'fullname' => 'Nama Lengkap',
            'pic' => 'Kontak',
            'isauthor' => 'Penulis',
            'iscustomer' => 'Pelanggan',
            'isemployee' => 'Pegawai',
            'isvendor' => 'Vendor',
            'ishospital' => 'Rumah Sakit',
            'publishercode' => 'Kode Penerbit',
            'bankname' => 'Nama Bank',
            'bankaccountno' => 'Nomor Akun Bank',
            'address' => 'Alamat',
            'cityid' => 'Kota',
            'email' => 'Email',
            'phoneno' => 'Telepon',
            'discount' => 'Diskon',
            'status' => 'Status',
            'createdAt' => 'Dibuat Pada',
            'updatedAt' => 'Diubah Pada',
            'createdBy' => 'Dibuat Oleh',
            'updatedBy' => 'Diubah Oleh',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['cityid' => 'cityid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['addressbookid' => 'addressbookid']);
    }
    
    public static function getFullName($addressbookid) {
        $model = self::findOne($addressbookid);
        if ($model) {
            return $model->fullname;
        }
        return NULL;
    }
}
