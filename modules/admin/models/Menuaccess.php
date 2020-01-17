<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "ms_menuaccess".
 *
 * @property int $menuaccessid
 * @property string $menuname
 * @property string $description
 * @property string $menuurl
 * @property string $menuicon
 * @property int $parentid
 * @property int $moduleid
 * @property int $sortorder
 * @property int $status
 *
 * @property Groupmenu[] $groupmenu
 * @property Groupaccess[] $groupaccess
 * @property Modules $module
 */
class Menuaccess extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ms_menuaccess';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['menuname', 'description', 'moduleid', 'sortorder'], 'required', 'on' => 'create'],
            [['menuname', 'description', 'moduleid', 'sortorder'], 'required', 'on' => 'update'],
            [['parentid', 'moduleid', 'sortorder', 'status'], 'integer'],
            [['menuname', 'description', 'menuurl', 'menuicon'], 'string', 'max' => 50],
            [['menuname'], 'unique'],
            [['moduleid'], 'exist', 'skipOnError' => true, 'targetClass' => Modules::className(), 'targetAttribute' => ['moduleid' => 'moduleid']],
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
            'menuaccessid' => 'ID',
            'menuname' => 'Nama',
            'description' => 'Deskripsi',
            'menuurl' => 'URL',
            'menuicon' => 'Ikon',
            'parentid' => 'Induk',
            'moduleid' => 'Modul',
            'sortorder' => 'Urutan',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupmenu()
    {
        return $this->hasMany(Groupmenu::className(), ['menuaccessid' => 'menuaccessid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupaccess()
    {
        return $this->hasMany(Groupaccess::className(), ['groupaccessid' => 'groupaccessid'])->viaTable('ms_groupmenu', ['menuaccessid' => 'menuaccessid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModule()
    {
        return $this->hasOne(Modules::className(), ['moduleid' => 'moduleid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuparent()
    {
        return $this->hasOne(Menuaccess::className(), ['menuaccessid' => 'parentid']);
    }
    
    public static function getMenuName($controller) {
        $model = self::findOne(['menuname' => $controller]);
        if ($model) {
            return $model->description;
        }
        return NULL;
    }
    
    public static function getFormName($menuname, $action) {
        $append = ($action == 'create') ? 'Tambah' : 'Ubah';
        $view = ($action == 'view') ? 'Detail' : $append;
        return $menuname . ' - ' . $view;
    }
    
    public static function gridTitle($title) {
        return 'Daftar ' . $title;
    }
    
    public static function gridBrowseCompanyTitle() {
        return 'Pilih perusahaan yang dituju';
    }
    
    public static function gridBrowseGoodsissueTitle() {
        return 'Pilih pengeluaran barang';
    }
    
    public static function gridBrowseInvoicearTitle() {
        return 'Pilih faktur penjualan';
    }
    
    public static function gridBrowseInvoiceapTitle() {
        return 'Pilih faktur pembelian';
    }
    
    public static function gridBrowseSlocTitle() {
        return 'Pilih gudang yang dituju';
    }
    
    public static function gridBrowseSalesorderTitle() {
        return 'Pilih pesanan penjualan';
    }
    
    public static function gridBrowsePurchaseorderTitle() {
        return 'Pilih pembelian';
    }
    
    public static function gridBrowseGoodsreceiptTitle() {
        return 'Pilih penerimaan barang';
    }
    
    public static function gridBrowseInvoiceroyaltyTitle() {
        return 'Pilih faktur royalti';
    }
}
