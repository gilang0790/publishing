<?php

namespace app\modules\order\models;

use app\modules\accounting\models\Invoicear;
use app\modules\common\models\Plant;
use Yii;

/**
 * This is the model class for table "tr_salesorderinfo".
 *
 * @property int $id
 * @property int $plantid
 * @property int $salesorderid
 * @property int $status
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 * 
 * @property Plant $plant
 */
class Salesorderinfo extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_salesorderinfo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['plantid', 'salesorderid', 'createdAt', 'createdBy'], 'required'],
            [['plantid', 'salesorderid', 'createdBy', 'updatedBy'], 'integer'],
            [['createdAt', 'updatedAt'], 'safe'],
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
            'id' => 'ID',
            'plantid' => 'Cabang',
            'salesorderid' => 'Nomor Penjualan',
            'status' => 'Status',
            'createdAt' => 'Dibuat Pada',
            'updatedAt' => 'Diubah Pada',
            'createdBy' => 'Dibuat Oleh',
            'updatedBy' => 'Diubah Oleh',
        ];
    }
    
    public function getPlant()
    {
        return $this->hasOne(Plant::className(), ['plantid' => 'plantid']);
    }
    
    public function getSalesorder()
    {
        return $this->hasOne(Salesorder::className(), ['id' => 'salesorderid']);
    }
    
    public static function getActionGrid($salesorderid) {
        $checkModel = Invoicear::find()
            ->innerJoinWith('goodsissue')
            ->andWhere(['tr_goodsissue.salesorderid' => $salesorderid])
            ->andWhere('tr_invoicear.status != :status', [':status' => 0])
            ->one();
        if ($checkModel) {
            return false;
        } else {
            return true;
        }
    }
}
