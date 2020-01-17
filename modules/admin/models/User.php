<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "ms_user".
 *
 * @property int $userID
 * @property string $username
 * @property string $fullName
 * @property string $authKey
 * @property string $passwordHash
 * @property string $email
 * @property int $status
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 *
 * @property MsUsergroup[] $msUsergroups
 * @property MsGroupaccess[] $groupaccesses
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    public $password_input;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ms_user';
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
            [['email', 'username', 'fullName', 'password_input'], 'required', 'on' => 'create'],
            [['username', 'fullName', 'email'], 'required', 'on' => 'update'],
            [['password_input'], 'required', 'on' => 'change'],
            [['createdAt', 'updatedAt', 'createdBy', 'updatedBy'], 'safe'],
            [['password_input'], 'string', 'min' => 6, 'max' => 50],
            [['username', 'email'], 'unique'],
            ['password_input', 'match', 'pattern' => '/^.*(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&?*()]).*$/',
                'message' => 'Invalid Password!'
            ],
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
            'userID' => 'ID',
            'username' => 'Nama Pengguna',
            'fullName' => 'Nama Lengkap',
            'email' => 'Email',
            'createdAt' => 'Dibuat Pada',
            'updatedAt' => 'Diubah Pada',
            'createdBy' => 'Dibuat Oleh',
            'updatedBy' => 'Diubah Oleh',
            'password_input' => 'Kata Sandi',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id) {
        return static::findOne(['userID' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username) {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password) {
        return Yii::$app->security->validatePassword($password, $this->passwordHash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password) {
        $this->passwordHash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey() {
        $this->authKey = Yii::$app->security->generateRandomString();
    }
    
    public function beforeSave($insert) {
    	if (parent::beforeSave($insert)) {
            if(!empty($this->password_input)){
                $this->generateAuthKey();
                $this->setPassword($this->password_input);
            }
            return true;
    	} 
    }
    
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $action = Yii::$app->controller->action->id . ' ' . Yii::$app->controller->id;
        $refNum = $this->username;
        if(Yii::$app->controller->action->id !== "login") {
            Yii::trace($action, 'TEST');
        }
    }
    
    public static function getUsername($userID) {
        $model = static::findOne($userID);
        if ($model) {
            return $model->username;
        }
        return '';
    }
}
