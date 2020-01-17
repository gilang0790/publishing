<?php

namespace app\modules\admin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ms_wfstatus".
 *
 * @property int $wfstatusid
 * @property int $workflowid
 * @property int $wfstat
 * @property string $wfstatusname
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 *
 * @property Workflow $workflow
 */
class Wfstatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ms_wfstatus';
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
            [['workflowid', 'wfstat', 'wfstatusname'], 'required', 'on' => 'create'],
            [['workflowid', 'wfstat', 'wfstatusname'], 'required', 'on' => 'update'],
            [['workflowid', 'wfstat'], 'integer'],
            [['wfstatusname'], 'string', 'max' => 50],
            [['createdAt', 'updatedAt', 'createdBy', 'updatedBy'], 'safe'],
            [['workflowid'], 'exist', 'skipOnError' => true, 'targetClass' => Workflow::className(), 'targetAttribute' => ['workflowid' => 'workflowid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'wfstatusid' => 'ID',
            'workflowid' => 'Alur Kerja',
            'wfstat' => 'Nomor Status',
            'wfstatusname' => 'Nama Status',
            'createdAt' => 'Dibuat Pada',
            'updatedAt' => 'Diubah Pada',
            'createdBy' => 'Dibuat Oleh',
            'updatedBy' => 'Diubah Oleh',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWorkflow()
    {
        return $this->hasOne(Workflow::className(), ['workflowid' => 'workflowid']);
    }
    
    public static function getStatusArray() {
        $wfname = NULL;
        $controller = Yii::$app->controller->id;
        if ($controller == 'stockopname') {
            $wfname = 'appbs';
        } else if ($controller == 'salesorder') {
            $wfname = 'appso';
        } else if ($controller == 'goodsissue') {
            $wfname = 'appgi';
        } else if ($controller == 'invoicear') {
            $wfname = 'appar';
        } else if ($controller == 'salespayment') {
            $wfname = 'appsp';
        } else if ($controller == 'purchaseorder') {
            $wfname = 'apppo';
        } else if ($controller == 'goodsreceipt') {
            $wfname = 'appgr';
        } else if ($controller == 'invoiceap') {
            $wfname = 'appap';
        } else if ($controller == 'purchasepayment') {
            $wfname = 'apppp';
        } else if ($controller == 'goodsissuereturn') {
            $wfname = 'appgir';
        } else if ($controller == 'goodsreceiptreturn') {
            $wfname = 'appgrr';
        } else if ($controller == 'advancepayment') {
            $wfname = 'appum';
        } else if ($controller == 'advanceroyalty') {
            $wfname = 'appumr';
        } else if ($controller == 'royaltypayment') {
            $wfname = 'apprp';
        }
        
        $model = self::find()
            ->select(['ms_wfstatus.wfstat', 'ms_wfstatus.wfstatusname'])
            ->join('INNER JOIN', 'ms_workflow', 'ms_workflow.workflowid = ms_wfstatus.workflowid')
            ->andWhere(['=', 'ms_workflow.wfname', $wfname])
            ->asArray()
            ->all();
        
        $result = [];
        if ($model) {
            foreach ($model as $data) {
                $result[$data['wfstat']] = $data['wfstatusname'];
            }
        }
        return $result;
    }
}
