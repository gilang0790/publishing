<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "ms_country".
 *
 * @property int $countryid
 * @property string $countrycode
 * @property string $countryname
 * @property int $status
 *
 * @property Province[] $provinces
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ms_country';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['countrycode', 'countryname', 'status'], 'required'],
            [['status'], 'integer'],
            [['countrycode'], 'string', 'max' => 5],
            [['countryname'], 'string', 'max' => 50],
            [['countrycode'], 'unique'],
            [['countryname'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'countryid' => 'ID',
            'countrycode' => 'Kode',
            'countryname' => 'Nama',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvinces()
    {
        return $this->hasMany(Province::className(), ['countryid' => 'countryid']);
    }
}
