<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "ms_transnumber".
 *
 * @property int $transnumberid
 * @property string $transtype
 * @property string $transabbreviation
 */
class Transnumber extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ms_transnumber';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['transtype', 'transabbreviation'], 'required'],
            [['transtype'], 'string', 'max' => 50],
            [['transabbreviation'], 'string', 'max' => 3],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'transnumberid' => 'Transnumberid',
            'transtype' => 'Transtype',
            'transabbreviation' => 'Transabbreviation',
        ];
    }
}
