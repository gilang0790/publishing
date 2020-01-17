<?php

namespace app\modules\admin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ms_groupmenuauth".
 *
 * @property string $groupmenuauthid
 * @property int $groupaccessid
 * @property int $menuauthid
 * @property string $menuvalueid
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 *
 * @property Groupaccess $groupaccess
 * @property Menuauth $menuauth
 */
class Groupmenuauth extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ms_groupmenuauth';
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
            [['groupaccessid', 'menuauthid', 'menuvalueid'], 'required', 'on' => 'create'],
            [['groupaccessid', 'menuauthid', 'menuvalueid'], 'required', 'on' => 'update'],
            [['groupaccessid', 'menuauthid'], 'integer'],
            [['menuvalueid'], 'string', 'max' => 50],
            [['createdAt', 'updatedAt', 'createdBy', 'updatedBy'], 'safe'],
            [['groupaccessid'], 'exist', 'skipOnError' => true, 'targetClass' => Groupaccess::className(), 'targetAttribute' => ['groupaccessid' => 'groupaccessid']],
            [['menuauthid'], 'exist', 'skipOnError' => true, 'targetClass' => Menuauth::className(), 'targetAttribute' => ['menuauthid' => 'menuauthid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'groupmenuauthid' => 'ID',
            'groupaccessid' => 'Grup',
            'menuauthid' => 'Objek',
            'menuvalueid' => 'Nilai',
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
    public function getMenuauth()
    {
        return $this->hasOne(Menuauth::className(), ['menuauthid' => 'menuauthid']);
    }
    
    public static function getObject($menuauth) {
        $model = self::find()
            ->select(['ms_groupmenuauth.menuvalueid as object'])
            ->join('INNER JOIN', 'ms_menuauth', 'ms_menuauth.menuauthid = ms_groupmenuauth.menuauthid')
            ->join('INNER JOIN', 'ms_usergroup', 'ms_usergroup.groupaccessid = ms_groupmenuauth.groupaccessid')
            ->andWhere(['=', 'ms_usergroup.userID', Yii::$app->user->identity->userID])
            ->andWhere(['=', 'ms_menuauth.menuobject', $menuauth])
            ->asArray()
            ->all();
        
        $strArray = [];
        $result = '';
        if ($model) {
            foreach ($model as $data) {
                $strArray[] = $data['object'];
            }
            
            if ($strArray) {
                foreach ($strArray as $val) {
                    if ($val == end($strArray)) {
                        $result .= "'$val'";
                    } else {
                        $result .= "'$val',";
                    }
                }
            }
            if ($strArray) {
                return '('.$result.')';
            }
        }
        return $result;
    }
}
