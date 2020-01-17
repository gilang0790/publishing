<?php

namespace app\modules\admin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ms_modules".
 *
 * @property int $moduleid
 * @property string $modulename
 * @property string $moduledesc
 * @property string $moduleicon
 * @property int $isinstall
 * @property int $status
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 *
 * @property Menuaccess[] $menuaccesses
 */
class Modules extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ms_modules';
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
            [['modulename', 'moduleicon'], 'required', 'on' => 'create'],
            [['modulename', 'moduleicon'], 'required', 'on' => 'update'],
            [['isinstall', 'status'], 'integer'],
            [['modulename', 'moduleicon'], 'string', 'max' => 50],
            [['moduledesc'], 'string', 'max' => 150],
            [['modulename'], 'unique'],
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
            'moduleid' => 'ID',
            'modulename' => 'Nama',
            'moduledesc' => 'Deskripsi',
            'moduleicon' => 'Ikon',
            'isinstall' => 'Instal?',
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
    public function getMenuaccesses()
    {
        return $this->hasMany(Menuaccess::className(), ['moduleid' => 'moduleid']);
    }
    
    public static function getActiveModule($modulename) {
        $model = self::find()->where(['modulename' => $modulename])->one();
        if ($model->isinstall == 1) {
            return true;
        }
        return false;
    }
}
