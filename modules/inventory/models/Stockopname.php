<?php

namespace app\modules\inventory\models;

use Yii;
use app\modules\common\models\Sloc;
use app\modules\admin\models\User;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tr_stockopname".
 *
 * @property int $id
 * @property string $bstransdate
 * @property string $bstransnum
 * @property int $slocid
 * @property string $headernote
 * @property string $total
 * @property int $status
 * @property string $lockDateUntil
 * @property int $lockBy
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 * 
 * @property StockopnameDetails[] $transactionDetails
 * @property Sloc $sloc
 * @property Createdby $createdby
 * @property Updatedby $updatedby
 */
class Stockopname extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_stockopname';
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
            [['bstransdate', 'slocid'], 'required'],
            [['bstransnum', 'bstransdate', 'createdAt', 'updatedAt', 'createdBy', 'updatedBy', 'total', 'headernote', 'lockDateUntil', 'lockBy'], 'safe'],
            [['slocid', 'status', 'createdBy', 'updatedBy', 'lockBy'], 'integer'],
            [['headernote'], 'string'],
            [['bstransnum'], 'string', 'max' => 20],
            [['slocid'], 'exist', 'skipOnError' => true, 'targetClass' => Sloc::className(), 'targetAttribute' => ['slocid' => 'slocid']]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bstransdate' => 'Tanggal Transaksi',
            'bstransnum' => 'Nomor Transaksi',
            'slocid' => 'Gudang',
            'headernote' => 'Catatan',
            'total' => 'Total (Rp)',
            'status' => 'Status',
            'lockDateUntil' => 'Dikunci hingga',
            'lockBy' => 'Dikunci Oleh',
            'createdAt' => 'Dibuat Pada',
            'updatedAt' => 'Diubah Pada',
            'createdBy' => 'Dibuat Oleh',
            'updatedBy' => 'Diubah Oleh',
        ];
    }
    
    public function getCreatedby()
    {
        return $this->hasOne(User::className(), ['userID' => 'createdBy']);
    }
    
    public function getUpdatedby()
    {
        return $this->hasOne(User::className(), ['userID' => 'updatedBy']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactionDetails()
    {
        return $this->hasMany(Stockopnamedetail::className(), ['head_id' => 'id']);
    }
    
    public function getSloc()
    {
        return $this->hasOne(Sloc::className(), ['slocid' => 'slocid']);
    }
    
    public function afterFind() {
        parent::afterFind();
        $this->total = round($this->total);
    }
}
