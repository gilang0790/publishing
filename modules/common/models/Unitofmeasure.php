<?php

namespace app\modules\common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ms_unitofmeasure".
 *
 * @property int $unitofmeasureid
 * @property string $uomcode
 * @property string $description
 * @property int $status
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 */
class Unitofmeasure extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ms_unitofmeasure';
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
            [['uomcode', 'description', 'status'], 'required', 'on' => 'create'],
            [['uomcode', 'description', 'status'], 'required', 'on' => 'update'],
            [['status'], 'integer'],
            [['uomcode'], 'string', 'max' => 5],
            [['description'], 'string', 'max' => 50],
            [['uomcode'], 'unique'],
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
            'unitofmeasureid' => 'ID',
            'uomcode' => 'Kode',
            'description' => 'Deskripsi',
            'status' => 'Status',
            'createdAt' => 'Dibuat Pada',
            'updatedAt' => 'Diubah Pada',
            'createdBy' => 'Dibuat Oleh',
            'updatedBy' => 'Diubah Oleh',
        ];
    }
    
    public function getProduct()
    {
        return $this->hasMany(Product::className(), ['productid' => 'productid']);
    }
}
