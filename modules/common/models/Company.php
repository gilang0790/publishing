<?php

namespace app\modules\common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use app\modules\admin\models\City;

/**
 * This is the model class for table "ms_company".
 *
 * @property int $companyid
 * @property string $companyname
 * @property string $companycode
 * @property string $address
 * @property int $cityid
 * @property string $zipcode
 * @property string $phoneno
 * @property string $webaddress
 * @property string $email
 * @property int $isholding
 * @property int $status
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 *
 * @property Plant[] $plants
 */
class Company extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ms_company';
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
            [['companyname', 'companycode'], 'required', 'on' => 'create'],
            [['companyname', 'companycode'], 'required', 'on' => 'update'],
            [['cityid', 'isholding', 'status'], 'integer'],
            [['companyname', 'phoneno'], 'string', 'max' => 50],
            [['companycode', 'zipcode'], 'string', 'max' => 10],
            [['address'], 'string', 'max' => 250],
            [['webaddress', 'email'], 'string', 'max' => 100],
            [['companyname'], 'unique'],
            [['createdAt', 'updatedAt', 'createdBy', 'updatedBy'], 'safe'],
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
            'companyid' => 'ID',
            'companyname' => 'Nama',
            'companycode' => 'Kode',
            'address' => 'Alamat',
            'cityid' => 'Kota',
            'zipcode' => 'Kantor Pos',
            'phoneno' => 'Telepon',
            'webaddress' => 'Website',
            'email' => 'Email',
            'isholding' => 'Holding',
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
    public function getPlants()
    {
        return $this->hasMany(Plant::className(), ['companyid' => 'companyid']);
    }
}
