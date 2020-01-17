<?php

namespace app\modules\common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ms_storagebin".
 *
 * @property int $storagebinid
 * @property int $slocid
 * @property string $description
 * @property int $ismultiproduct
 * @property string $qtymax
 * @property int $status
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 *
 * @property Sloc $sloc
 */
class Storagebin extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ms_storagebin';
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
            [['slocid', 'description', 'status'], 'required', 'on' => 'create'],
            [['slocid', 'description', 'status'], 'required', 'on' => 'update'],
            [['slocid', 'ismultiproduct', 'status'], 'integer'],
            [['qtymax'], 'number'],
            [['description'], 'string', 'max' => 50],
            [['createdAt', 'updatedAt', 'createdBy', 'updatedBy'], 'safe'],
            [['description', 'slocid'], 'unique', 'targetAttribute' => ['description', 'slocid']],
            [['slocid'], 'exist', 'skipOnError' => true, 'targetClass' => Sloc::className(), 'targetAttribute' => ['slocid' => 'slocid']],
            [['status', 'ismultiproduct'], 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'storagebinid' => 'ID',
            'slocid' => 'Gudang',
            'description' => 'Rak',
            'ismultiproduct' => 'Multi Barang',
            'qtymax' => 'Jumlah Maksimal',
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
    public function getSloc()
    {
        return $this->hasOne(Sloc::className(), ['slocid' => 'slocid']);
    }
}
