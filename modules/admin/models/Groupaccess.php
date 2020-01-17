<?php

namespace app\modules\admin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ms_groupaccess".
 *
 * @property int $groupaccessid
 * @property string $groupname
 * @property int $status
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 *
 * @property Groupmenu[] $groupmenus
 * @property Menuaccess[] $menuaccesses
 * @property Groupmenuauth[] $groupmenuauths
 * @property Usergroup[] $usergroups
 * @property User[] $users
 * @property Wfgroup[] $wfgroups
 */
class Groupaccess extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ms_groupaccess';
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
            [['groupname'], 'required', 'on' => 'create'],
            [['groupname'], 'required', 'on' => 'update'],
            [['status'], 'integer'],
            [['groupname'], 'string', 'max' => 50],
            [['groupname'], 'unique'],
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
            'groupaccessid' => 'ID',
            'groupname' => 'Nama',
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
    public function getMsGroupmenus()
    {
        return $this->hasMany(Groupmenu::className(), ['groupaccessid' => 'groupaccessid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuaccesses()
    {
        return $this->hasMany(Menuaccess::className(), ['menuaccessid' => 'menuaccessid'])->viaTable('ms_groupmenu', ['groupaccessid' => 'groupaccessid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupmenuauths()
    {
        return $this->hasMany(Groupmenuauth::className(), ['groupaccessid' => 'groupaccessid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsergroups()
    {
        return $this->hasMany(Usergroup::className(), ['groupaccessid' => 'groupaccessid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['userID' => 'userID'])->viaTable('ms_usergroup', ['groupaccessid' => 'groupaccessid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWfgroups()
    {
        return $this->hasMany(Wfgroup::className(), ['groupaccessid' => 'groupaccessid']);
    }
}
