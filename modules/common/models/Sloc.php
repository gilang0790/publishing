<?php

namespace app\modules\common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ms_sloc".
 *
 * @property int $slocid
 * @property int $plantid
 * @property string $sloccode
 * @property string $description
 * @property int $status
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 *
 * @property Plant $plant
 * @property Storagebin[] $storagebin
 */
class Sloc extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ms_sloc';
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
            [['plantid', 'sloccode', 'description', 'status'], 'required', 'on' => 'create'],
            [['plantid', 'sloccode', 'description', 'status'], 'required', 'on' => 'update'],
            [['plantid', 'status'], 'integer'],
            [['sloccode'], 'string', 'max' => 20],
            [['description'], 'string', 'max' => 50],
            [['sloccode'], 'unique'],
            [['createdAt', 'updatedAt', 'createdBy', 'updatedBy'], 'safe'],
            [['plantid'], 'exist', 'skipOnError' => true, 'targetClass' => Plant::className(), 'targetAttribute' => ['plantid' => 'plantid']],
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
            'slocid' => 'ID',
            'plantid' => 'Kantor Cabang',
            'sloccode' => 'Kode',
            'description' => 'Deskripsi',
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
    public function getPlant()
    {
        return $this->hasOne(Plant::className(), ['plantid' => 'plantid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStoragebin()
    {
        return $this->hasMany(Storagebin::className(), ['slocid' => 'slocid']);
    }
}
