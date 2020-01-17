<?php

namespace app\modules\admin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * This is the model class for table "ms_groupmenu".
 *
 * @property int $groupmenuid
 * @property int $groupaccessid
 * @property int $menuaccessid
 * @property int $isread
 * @property int $iswrite
 * @property int $ispost
 * @property int $isreject
 * @property int $isupload
 * @property int $isdownload
 * @property int $ispurge
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 *
 * @property Groupaccess $groupaccess
 * @property Menuaccess $menuaccess
 */
class Groupmenu extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ms_groupmenu';
    }public function behaviors() {
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
            [['groupaccessid'], 'required', 'on' => 'create'],
            [['groupaccessid'], 'required', 'on' => 'update'],
            [['groupaccessid', 'isread', 'iswrite', 'ispost', 'isreject', 'isupload', 'isdownload', 'ispurge'], 'integer'],
            [['createdAt', 'updatedAt', 'createdBy', 'updatedBy'], 'safe'],
            [['groupaccessid'], 'exist', 'skipOnError' => true, 'targetClass' => Groupaccess::className(), 'targetAttribute' => ['groupaccessid' => 'groupaccessid']],
            [['menuaccessid'], 'exist', 'skipOnError' => true, 'targetClass' => Menuaccess::className(), 'targetAttribute' => ['menuaccessid' => 'menuaccessid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'groupmenuid' => 'ID',
            'groupaccessid' => 'Grup',
            'menuaccessid' => 'Menu',
            'isread' => 'Baca',
            'iswrite' => 'Tulis',
            'ispost' => 'Setuju',
            'isreject' => 'Tolak',
            'isupload' => 'Unggah',
            'isdownload' => 'Unduh',
            'ispurge' => 'Hapus',
            'createdAt' => 'Dibuat Pada',
            'updatedAt' => 'Diubah Pada',
            'createdBy' => 'Dibuat Oleh',
            'updatedBy' => 'Diubah Oleh',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupaccess()
    {
        return $this->hasOne(Groupaccess::className(), ['groupaccessid' => 'groupaccessid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuaccess()
    {
        return $this->hasOne(Menuaccess::className(), ['menuaccessid' => 'menuaccessid']);
    }
    
    protected function findGroupmenuModel($groupid, $menuid)
    {
        $model = self::find()->where("groupaccessid = :groupaccessid AND menuaccessid = :menuaccessid", 
                [':groupaccessid'=>$groupid, ':menuaccessid'=>$menuid])->one();
        if ($model) {
            return $model;
        }
        return NULL;
    }
}
