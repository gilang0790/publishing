<?php

namespace app\modules\common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use app\modules\common\models\Company;

/**
 * This is the model class for table "ms_plant".
 *
 * @property int $plantid
 * @property string $plantcode
 * @property string $description
 * @property int $companyid
 * @property int $status
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 *
 * @property Company $company
 * @property Sloc[] $sloc
 */
class Plant extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ms_plant';
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
            [['plantcode', 'description'], 'required', 'on' => 'create'],
            [['plantcode', 'description'], 'required', 'on' => 'update'],
            [['companyid', 'status'], 'integer'],
            [['plantcode'], 'string', 'max' => 10],
            [['description'], 'string', 'max' => 50],
            [['plantcode'], 'unique'],
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
            'plantid' => 'ID',
            'plantcode' => 'Kode',
            'description' => 'Deskripsi',
            'companyid' => 'Perusahaan',
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
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['companyid' => 'companyid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSloc()
    {
        return $this->hasMany(Sloc::className(), ['plantid' => 'plantid']);
    }
}
