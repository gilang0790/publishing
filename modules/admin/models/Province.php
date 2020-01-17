<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "ms_province".
 *
 * @property int $provinceid
 * @property int $countryid
 * @property string $provincecode
 * @property string $provincename
 * @property int $status
 *
 * @property City[] $cities
 * @property MsCountry $country
 */
class Province extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ms_province';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['countryid', 'provincename', 'status'], 'required'],
            [['countryid', 'status'], 'integer'],
            [['provincecode'], 'string', 'max' => 5],
            [['provincename'], 'string', 'max' => 100],
            [['countryid', 'provincename'], 'unique', 'targetAttribute' => ['countryid', 'provincename']],
            [['countryid'], 'exist', 'skipOnError' => true, 'targetClass' => MsCountry::className(), 'targetAttribute' => ['countryid' => 'countryid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'provinceid' => 'ID',
            'countryid' => 'Negara',
            'provincecode' => 'Kode',
            'provincename' => 'Nama',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCities()
    {
        return $this->hasMany(City::className(), ['provinceid' => 'provinceid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['countryid' => 'countryid']);
    }
}
