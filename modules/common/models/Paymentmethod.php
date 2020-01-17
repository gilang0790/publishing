<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "ms_paymentmethod".
 *
 * @property int $paymentmethodid
 * @property string $paycode
 * @property int $paydays
 * @property string $paymentname
 * @property int $status
 */
class Paymentmethod extends \yii\db\ActiveRecord
{
    CONST CASH = 1;
    CONST CREDIT = 2;
    CONST NON_SALES = 3;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ms_paymentmethod';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['paydays', 'status'], 'required'],
            [['paydays', 'status'], 'integer'],
            [['paycode'], 'string', 'max' => 5],
            [['paymentname'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'paymentmethodid' => 'ID',
            'paycode' => 'Kode',
            'paydays' => 'Hari',
            'paymentname' => 'Nama',
            'status' => 'Status',
        ];
    }
}
