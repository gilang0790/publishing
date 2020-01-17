<?php

namespace app\modules\common\models;

use kartik\grid\GridView;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ms_product".
 *
 * @property int $productid
 * @property string $productname
 * @property string $productcode
 * @property int $categoryid
 * @property int $unitofmeasureid
 * @property string $isbn
 * @property string $author
 * @property string $size
 * @property string $weight
 * @property string $notes
 * @property int $type
 * @property int $status
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 *
 * @property Category $category
 */
class Product extends ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STOCK = 1;
    const SERVICE = 2;
    public static $type_array = ['1' => 'Barang', '2' => 'Jasa'];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ms_product';
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
            [['productname', 'productcode', 'categoryid', 'unitofmeasureid', 'type'], 'required', 'on' => 'create'],
            [['productname', 'productcode', 'categoryid', 'unitofmeasureid', 'type'], 'required', 'on' => 'update'],
            [['categoryid', 'unitofmeasureid', 'type', 'status'], 'integer'],
            [['productname', 'author'], 'string', 'max' => 250],
            [['productcode', 'isbn', 'size', 'weight'], 'string', 'max' => 45],
            [['notes'], 'string'],
            [['productname'], 'unique'],
            [['createdAt', 'updatedAt', 'createdBy', 'updatedBy'], 'safe'],
            [['categoryid'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['categoryid' => 'categoryid']],
            [['unitofmeasureid'], 'exist', 'skipOnError' => true, 'targetClass' => Unitofmeasure::className(), 'targetAttribute' => ['unitofmeasureid' => 'unitofmeasureid']],
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
            'productid' => 'ID',
            'productname' => 'Nama',
            'productcode' => 'Kode',
            'categoryid' => 'Kategori',
            'unitofmeasureid' => 'Satuan',
            'isbn' => 'ISBN',
            'author' => 'Penulis',
            'size' => 'Ukuran',
            'weight' => 'Berat',
            'type' => 'Tipe',
            'notes' => 'Catatan',
            'status' => 'Status',
            'createdAt' => 'Dibuat Pada',
            'updatedAt' => 'Diubah Pada',
            'createdBy' => 'Dibuat Oleh',
            'updatedBy' => 'Diubah Oleh',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['categoryid' => 'categoryid']);
    }
    
    public function getUom()
    {
        return $this->hasOne(Unitofmeasure::className(), ['unitofmeasureid' => 'unitofmeasureid']);
    }
    
    public static function getProductName($productid) {
        $model = self::findOne($productid);
        if ($model) {
            return $model->productname;
        }
        return NULL;
    }
    
    public static function getTypeColumn() {
        return [
            'attribute' => 'type',
            'width' => '10%',
            'headerOptions' => [
                'class' => 'text-center'
            ],
            'vAlign' => 'middle',
            'hAlign' => 'center',
            'value' => function ($data) {
                $type = self::$type_array;
                return $type[$data->type];
            },
            'filter' => self::$type_array,
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'pluginOptions' => [
                    'allowClear' => true,
                    'placeholder' => ''
                ]
            ]
        ];
    }
}
