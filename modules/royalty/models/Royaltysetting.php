<?php

namespace app\modules\royalty\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use app\modules\common\models\Author;
use app\modules\common\models\Product;

/**
 * This is the model class for table "ms_royaltysetting".
 *
 * @property int $royaltysettingid
 * @property int $addressbookid
 * @property int $productid
 * @property int $period
 * @property string $fee
 * @property string $tax
 * @property string $notes
 * @property int $status
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 *
 * @property Author $author
 * @property Product $product
 */
class Royaltysetting extends ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ms_royaltysetting';
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
            [['addressbookid', 'productid', 'period', 'fee', 'status'], 'required', 'on' => 'create'],
            [['addressbookid', 'productid', 'period', 'fee', 'status'], 'required', 'on' => 'update'],
            [['addressbookid', 'productid', 'period', 'createdBy', 'updatedBy'], 'integer'],
            [['fee', 'tax'], 'number'],
            [['notes'], 'string'],
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
            'royaltysettingid' => 'ID',
            'addressbookid' => 'Penulis',
            'productid' => 'Buku',
            'period' => 'Periode (Bulan)',
            'fee' => 'Royalty (%)',
            'tax' => 'Pajak (%)',
            'notes' => 'Catatan',
            'status' => 'Status',
            'createdAt' => 'Dibuat Pada',
            'updatedAt' => 'Diubah Pada',
            'createdBy' => 'Dibuat Oleh',
            'updatedBy' => 'Diubah Oleh',
        ];
    }
    
    public function getAuthor()
    {
        return $this->hasOne(Author::className(), ['addressbookid' => 'addressbookid']);
    }
    
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['productid' => 'productid']);
    }
    
    public function afterFind() {
        parent::afterFind();
        $this->fee = round($this->fee);
        $this->tax = round($this->tax);
    }
}
