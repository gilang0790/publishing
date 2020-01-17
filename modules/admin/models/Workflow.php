<?php

namespace app\modules\admin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ms_workflow".
 *
 * @property int $workflowid
 * @property string $wfname
 * @property string $wfdesc wf description
 * @property int $wfminstat
 * @property int $wfmaxstat
 * @property int $
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBystatus
 *
 * @property Wfgroup[] $wfgroups
 * @property Wfstatus[] $wfstatuses
 */
class Workflow extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ms_workflow';
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
            [['wfname', 'wfdesc', 'wfminstat', 'wfmaxstat'], 'required', 'on' => 'create'],
            [['wfname', 'wfdesc', 'wfminstat', 'wfmaxstat'], 'required', 'on' => 'update'],
            [['wfminstat', 'wfmaxstat', 'status'], 'integer'],
            [['wfname'], 'string', 'max' => 20],
            [['wfdesc'], 'string', 'max' => 50],
            [['wfname'], 'unique'],
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
            'workflowid' => 'ID',
            'wfname' => 'Nama',
            'wfdesc' => 'Deskripsi',
            'wfminstat' => 'Minimal',
            'wfmaxstat' => 'Maksimal',
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
    public function getWfgroups()
    {
        return $this->hasMany(Wfgroup::className(), ['workflowid' => 'workflowid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWfstatuses()
    {
        return $this->hasMany(Wfstatus::className(), ['workflowid' => 'workflowid']);
    }
}
