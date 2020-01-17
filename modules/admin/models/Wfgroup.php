<?php

namespace app\modules\admin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "ms_wfgroup".
 *
 * @property int $wfgroupid
 * @property int $workflowid
 * @property int $groupaccessid
 * @property int $wfbefstat
 * @property int $wfrecstat
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 *
 * @property Groupaccess $groupaccess
 * @property Workflow $workflow
 */
class Wfgroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ms_wfgroup';
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
            [['workflowid', 'groupaccessid', 'wfbefstat', 'wfrecstat'], 'required', 'on' => 'create'],
            [['workflowid', 'groupaccessid', 'wfbefstat', 'wfrecstat'], 'required', 'on' => 'update'],
            [['workflowid', 'groupaccessid', 'wfbefstat', 'wfrecstat'], 'integer'],
            [['createdAt', 'updatedAt', 'createdBy', 'updatedBy'], 'safe'],
            [['workflowid', 'groupaccessid', 'wfbefstat'], 'unique', 'targetAttribute' => ['workflowid', 'groupaccessid', 'wfbefstat']],
            [['groupaccessid'], 'exist', 'skipOnError' => true, 'targetClass' => Groupaccess::className(), 'targetAttribute' => ['groupaccessid' => 'groupaccessid']],
            [['workflowid'], 'exist', 'skipOnError' => true, 'targetClass' => Workflow::className(), 'targetAttribute' => ['workflowid' => 'workflowid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'wfgroupid' => 'ID',
            'workflowid' => 'Alur Kerja',
            'groupaccessid' => 'Grup',
            'wfbefstat' => 'Status Sebelum Proses',
            'wfrecstat' => 'Status Sesudah Proses',
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
    public function getWorkflow()
    {
        return $this->hasOne(Workflow::className(), ['workflowid' => 'workflowid']);
    }
    
    public static function getStatusList($wfname) {
        $userLogin = Yii::$app->user->identity->userID;
        $model = self::find()
            ->select(['ms_wfgroup.wfrecstat as status'])
            ->join('INNER JOIN', 'ms_groupaccess', 'ms_groupaccess.groupaccessid = ms_wfgroup.groupaccessid')
            ->join('INNER JOIN', 'ms_usergroup', 'ms_usergroup.groupaccessid = ms_groupaccess.groupaccessid')
            ->join('INNER JOIN', 'ms_workflow', 'ms_workflow.workflowid = ms_wfgroup.workflowid')
            ->andWhere(['=', 'ms_usergroup.userID', $userLogin])
            ->andWhere(['=', 'ms_workflow.wfname', $wfname])
            ->asArray()
            ->all();
        
        $strArray = [];
        $result = '';
        if ($model) {
            foreach ($model as $data) {
                $strArray[] = $data['status'];
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
    
    public static function getNextStatus($wfname, $wfbefstat) {
        $result = NULL;
        $userLogin = Yii::$app->user->identity->userID;
        $model = self::find()
            ->select(['ms_wfgroup.wfrecstat'])
            ->join('INNER JOIN', 'ms_usergroup', 'ms_usergroup.groupaccessid = ms_wfgroup.groupaccessid')
            ->join('INNER JOIN', 'ms_workflow', 'ms_workflow.workflowid = ms_wfgroup.workflowid')
            ->andWhere(['=', 'ms_usergroup.userID', $userLogin])
            ->andWhere(['=', 'ms_workflow.wfname', $wfname])
            ->andWhere(['=', 'ms_wfgroup.wfbefstat', $wfbefstat])
            ->one();
        
        if ($model) {
            $result = $model->wfrecstat;
        }
        return $result;
    }
    
    public static function getMaxStatus($wfname=null) {
        if (!$wfname) {
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
        }
        $result = NULL;
        $model = self::find()
            ->select(['wfrecstat' => new Expression('MAX(ms_wfgroup.wfrecstat)')])
            ->join('INNER JOIN', 'ms_usergroup', 'ms_usergroup.groupaccessid = ms_wfgroup.groupaccessid')
            ->join('INNER JOIN', 'ms_workflow', 'ms_workflow.workflowid = ms_wfgroup.workflowid')
            ->andWhere(['=', 'ms_workflow.wfname', $wfname])
            ->one();
        
        if ($model) {
            $result = $model->wfrecstat;
        }
        return $result;
    }
    
    public static function getMinStatus($wfname=null) {
        if (!$wfname) {
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
        }
        $result = NULL;
        $model = self::find()
            ->select(['wfbefstat' => new Expression('MIN(ms_wfgroup.wfbefstat)')])
            ->join('INNER JOIN', 'ms_usergroup', 'ms_usergroup.groupaccessid = ms_wfgroup.groupaccessid')
            ->join('INNER JOIN', 'ms_workflow', 'ms_workflow.workflowid = ms_wfgroup.workflowid')
            ->andWhere(['=', 'ms_workflow.wfname', $wfname])
            ->one();
        
        if ($model) {
            $result = $model->wfbefstat;
        }
        return $result;
    }
}
