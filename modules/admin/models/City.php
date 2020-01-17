<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "ms_city".
 *
 * @property int $cityid
 * @property int $provinceid
 * @property string $citycode
 * @property string $cityname
 * @property int $status
 *
 * @property Province $province
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ms_city';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['provinceid', 'cityname', 'status'], 'required'],
            [['provinceid', 'status'], 'integer'],
            [['citycode'], 'string', 'max' => 5],
            [['cityname'], 'string', 'max' => 50],
            [['provinceid', 'cityname'], 'unique', 'targetAttribute' => ['provinceid', 'cityname']],
            [['provinceid'], 'exist', 'skipOnError' => true, 'targetClass' => Province::className(), 'targetAttribute' => ['provinceid' => 'provinceid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cityid' => 'ID',
            'provinceid' => 'Provinsi',
            'citycode' => 'Kode',
            'cityname' => 'Nama',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvince()
    {
        return $this->hasOne(Province::className(), ['provinceid' => 'provinceid']);
    }
}
