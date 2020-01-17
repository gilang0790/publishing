<?php

namespace app\components;

use app\modules\order\models\Salesorderinfo;
use app\modules\admin\models\Transnumber;
use app\modules\admin\models\Wfgroup;
use app\modules\admin\models\Wfstatus;
use DateTime;
use kartik\grid\GridView;
use kartik\widgets\DatePicker;
use Yii;
use yii\db\Expression;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\validators\DateValidator;
use yii\helpers\StringHelper;

class AppHelper {

    public static $activeStatus = ['1' => 'Aktif', '0' => 'Tidak Aktif'];
    public static $isMultiProduct = ['1' => 'Ya', '0' => 'Tidak'];
    public static $typeStockOpname = ['plus' => 'Tambah', 'minus' => 'Kurang'];
    public static $salesType = ['1' => 'Offline', '2' => 'Online'];
    
    public static function createNewTransactionNumber($transType, $transDate, $additionalCode = "") {
        $newTransNum = '';
        
        if ($transType == 'Stock Opname') {
            $className = 'app\modules\inventory\models\Stockopname';
            $keyColumnName = 'bstransnum';
        } else if ($transType == 'Sales Order') {
            $className = 'app\modules\order\models\Salesorder';
            $keyColumnName = 'sotransnum';
        } else if ($transType == 'Goods Issue') {
            $className = 'app\modules\inventory\models\Goodsissue';
            $keyColumnName = 'gitransnum';
        } else if ($transType == 'Account Receivable') {
            $className = 'app\modules\accounting\models\Invoicear';
            $keyColumnName = 'artransnum';
        } else if ($transType == 'Sales Payment') {
            $className = 'app\modules\accounting\models\Salespayment';
            $keyColumnName = 'sptransnum';
        } else if ($transType == 'Purchase Order') {
            $className = 'app\modules\purchase\models\Purchaseorder';
            $keyColumnName = 'potransnum';
        } else if ($transType == 'Goods Receipt') {
            $className = 'app\modules\inventory\models\Goodsreceipt';
            $keyColumnName = 'grtransnum';
        } else if ($transType == 'Account Payable') {
            $className = 'app\modules\accounting\models\Invoiceap';
            $keyColumnName = 'aptransnum';
        } else if ($transType == 'Purchase Payment') {
            $className = 'app\modules\accounting\models\Purchasepayment';
            $keyColumnName = 'pptransnum';
        } else if ($transType == 'Goods Issue Return') {
            $className = 'app\modules\inventory\models\Goodsissuereturn';
            $keyColumnName = 'girtransnum';
        } else if ($transType == 'Goods Receipt Return') {
            $className = 'app\modules\inventory\models\Goodsreceiptreturn';
            $keyColumnName = 'grrtransnum';
        } else if ($transType == 'Advance Payment') {
            $className = 'app\modules\accounting\models\Advancepayment';
            $keyColumnName = 'umtransnum';
        } else if ($transType == 'Advance Royalty') {
            $className = 'app\modules\royalty\models\Advanceroyalty';
            $keyColumnName = 'umrtransnum';
        } else if ($transType == 'Royalty Account Payable') {
            $className = 'app\modules\royalty\models\Invoiceroyalty';
            $keyColumnName = 'transnum';
        } else if ($transType == 'Royalty Payment') {
            $className = 'app\modules\royalty\models\Royaltypayment';
            $keyColumnName = 'rptransnum';
        }
            
        $transModel = Transnumber::find()
        ->where(['transtype' => $transType])
        ->one();
        if (!empty($transModel)) {
            $transNumAbbreviation = $transModel->transabbreviation;
            if (!empty($additionalCode)) {
                $transNumAbbreviation .= "/" . $additionalCode . "/";
            }
        }

        $transNumCheck = $transNumAbbreviation . date("Y", strtotime($transDate)) . date("m",
            strtotime($transDate)) . date("d", strtotime($transDate));

        $tempModel = $className::find()
            ->where(['like', $keyColumnName, $transNumCheck])
            ->orderBy("$keyColumnName DESC")
            ->one();

        if ($tempModel) {
            $newTransNum = $transNumAbbreviation . strval(substr($tempModel[$keyColumnName],
                        strlen($tempModel[$keyColumnName]) - 12, 12) + 1);
        } else {
            $newTransNum = $transNumCheck . '0001';
        }

        return $newTransNum;
    }
    
    public static function canAccessData($transType, $transNum) {
        $result = TRUE;
        $className = NULL;
        $userLogin = Yii::$app->user->identity->userID;
        $keyColumnName = NULL;
        $lockDateUntilColumnName = 'lockDateUntil';
        
        if ($transType == 'Stock Opname') {
            $className = 'app\modules\inventory\models\Stockopname';
            $keyColumnName = 'bstransnum';
        } else if ($transType == 'Sales Order') {
            $className = 'app\modules\order\models\Salesorder';
            $keyColumnName = 'sotransnum';
        } else if ($transType == 'Goods Issue') {
            $className = 'app\modules\inventory\models\Goodsissue';
            $keyColumnName = 'gitransnum';
        } else if ($transType == 'Account Receivable') {
            $className = 'app\modules\accounting\models\Invoicear';
            $keyColumnName = 'artransnum';
        } else if ($transType == 'Sales Payment') {
            $className = 'app\modules\accounting\models\Salespayment';
            $keyColumnName = 'sptransnum';
        } else if ($transType == 'Purchase Order') {
            $className = 'app\modules\purchase\models\Purchaseorder';
            $keyColumnName = 'potransnum';
        } else if ($transType == 'Goods Receipt') {
            $className = 'app\modules\inventory\models\Goodsreceipt';
            $keyColumnName = 'grtransnum';
        } else if ($transType == 'Account Payable') {
            $className = 'app\modules\accounting\models\Invoiceap';
            $keyColumnName = 'aptransnum';
        } else if ($transType == 'Purchase Payment') {
            $className = 'app\modules\accounting\models\Purchasepayment';
            $keyColumnName = 'pptransnum';
        } else if ($transType == 'Goods Issue Return') {
            $className = 'app\modules\inventory\models\Goodsissuereturn';
            $keyColumnName = 'girtransnum';
        } else if ($transType == 'Goods Receipt Return') {
            $className = 'app\modules\inventory\models\Goodsreceiptreturn';
            $keyColumnName = 'grrtransnum';
        } else if ($transType == 'Advance Payment') {
            $className = 'app\modules\accounting\models\Advancepayment';
            $keyColumnName = 'umtransnum';
        } else if ($transType == 'Advance Royalty') {
            $className = 'app\modules\royalty\models\Advanceroyalty';
            $keyColumnName = 'umrtransnum';
        } else if ($transType == 'Royalty Account Payable') {
            $className = 'app\modules\royalty\models\Invoiceroyalty';
            $keyColumnName = 'transnum';
        } else if ($transType == 'Royalty Payment') {
            $className = 'app\modules\royalty\models\Royaltypayment';
            $keyColumnName = 'rptransnum';
        }
        
        $tempModel = $className::find()
            ->where("$keyColumnName = '$transNum' AND $lockDateUntilColumnName > NOW() AND lockBy <> $userLogin")
            ->one();
        if ($tempModel) {
            $result = FALSE;
        }
        
        if ($result) {
            $saveModel = $className::find()
                ->where("$keyColumnName = '$transNum'")
                ->one();
            if ($saveModel) {
                $stringTime = date_create(date('Y-m-d H:i:s'));
                $added = date_add($stringTime, date_interval_create_from_date_string("900 seconds"));
                $saveModel->lockDateUntil = date_format($added, 'Y-m-d H:i:s');
                $saveModel->lockBy = Yii::$app->user->identity->userID;
                $saveModel->save();
            }
        }
        return $result;
    }
    
    public static function getDataWfStatus() {
        return [
            'attribute' => 'status',
            'width' => '10%',
            'headerOptions' => [
                'class' => 'text-center'
            ],
            'vAlign' => 'middle',
            'hAlign' => 'center',
            'value' => function ($data) {
                $arrayStatus = Wfstatus::getStatusArray();
                return $arrayStatus[$data->status];
            },
            'filter' => Wfstatus::getStatusArray(),
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'pluginOptions' => [
                    'allowClear' => true,
                    'placeholder' => 'Pilih'
                ]
            ]
        ];
    }
    
    public static function getDataSalesType() {
        return [
            'attribute' => 'salestype',
            'width' => '10%',
            'headerOptions' => [
                'class' => 'text-center'
            ],
            'vAlign' => 'middle',
            'hAlign' => 'center',
            'value' => function ($data) {
                $salesTypeStatus = self::$salesType;
                return $salesTypeStatus[$data->salestype];
            },
            'filter' => self::$salesType,
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'pluginOptions' => [
                    'allowClear' => true,
                    'placeholder' => 'Pilih'
                ]
            ]
        ];
    }
    
    public static function getMenuList()
    {
        $db = Yii::$app->db;
        $sql = "SELECT distinct a.menuicon,a.menuname, a.menuaccessid, a.description, a.menuurl,a.parentid,a.sortorder,a.description
                    FROM ms_menuaccess a
                    INNER JOIN ms_groupmenu b ON b.menuaccessid = a.menuaccessid
                    INNER JOIN ms_usergroup c ON c.groupaccessid = b.groupaccessid
                    INNER JOIN ms_user d ON d.userID = c.userID
                    WHERE a.parentid is null and a.status = 1 and b.isread = 1 and LOWER(d.username) = LOWER('".Yii::$app->user->identity->username."')
                    ORDER BY a.sortorder ASC, a.description ASC";
        $createCommand = $db->createCommand($sql);
        return $createCommand->queryAll();
    }
    
    public static function getSubmenuList($menuaccessid)
    {
        $db = Yii::$app->db;
        $sql = "SELECT distinct a.menuicon,a.menuname, a.menuaccessid, a.description, a.menuurl,a.parentid,a.sortorder,a.description
                    FROM ms_menuaccess a
                    JOIN ms_groupmenu b on b.menuaccessid = a.menuaccessid 
                    JOIN ms_usergroup c on c.groupaccessid = b.groupaccessid 
                    JOIN ms_user d on d.userID = c.userID
                    WHERE a.parentid = ".$menuaccessid." and a.status = 1 and b.isread = 1 and LOWER(d.username) = LOWER('".Yii::$app->user->identity->username."')
                    ORDER BY a.sortorder ASC, a.description ASC";
        $createCommand = $db->createCommand($sql);
        return $createCommand->queryAll();
    }

    public static function getDatePickerConfig($additional = []) {
        $config = [
            'type' => DatePicker::TYPE_COMPONENT_PREPEND,
            'pluginOptions' => [
                'format' => 'dd-mm-yyyy',
                'autoWidget' => true,
                'autoclose' => true,
                'todayBtn' => true,
                'startDate' => '-150y',
                'todayHighlight' => true
            ]
        ];

        $config = array_merge($config, $additional);
        return $config;
    }

    public static function convertDateTimeFormat($date, $formatFrom = "d-m-Y", $formatTo = "Y-m-d") {
        if (!empty($date)) {
            if (self::isValidDate($date, $formatFrom)) {
                $myDateTime = DateTime::createFromFormat($formatFrom, $date);
                return $myDateTime->format($formatTo);
            } else {
                return "";
            }
        } else {
            return "";
        }
    }

    public static function isValidDate($date, $format) {
        $validator = new DateValidator();
        $validator->format = "php:" . $format;
        return $validator->validate($date);
    }

    public static function getIsActiveColumn() {
        return [
            'attribute' => 'status',
            'width' => '12%',
            'headerOptions' => [
                'class' => 'text-center'
            ],
            'vAlign' => 'middle',
            'hAlign' => 'center',
            'value' => function ($data) {
                $activeStatus = self::$activeStatus;
                return $activeStatus[$data->status];
            },
            'filter' => self::$activeStatus,
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'pluginOptions' => [
                    'allowClear' => true,
                    'placeholder' => ''
                ]
            ]
        ];
    }

    public static function getIsMultiProductColumn() {
        return [
            'attribute' => 'ismultiproduct',
            'width' => '12%',
            'headerOptions' => [
                'class' => 'text-center'
            ],
            'vAlign' => 'middle',
            'hAlign' => 'center',
            'value' => function ($data) {
                $activeStatus = self::$isMultiProduct;
                return $activeStatus[$data->status];
            },
            'filter' => self::$isMultiProduct,
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'pluginOptions' => [
                    'allowClear' => true,
                    'placeholder' => ''
                ]
            ]
        ];
    }

    public static function getActionGrid($templateArray, $customButtons = [], $columnOptions = []) {
        $allowed = [];
        foreach ($templateArray as $item) {
            if (is_array($item)) {
                $controller = $item[0];
                $action = $item[1];
                $templateButton = $item[2];

                if (AccessHelper::hasAccess($action, $controller)) {
                    $allowed[] = "{" . $templateButton . "}";
                }
            } else {
                if (AccessHelper::hasAccess($item)) {
                    $allowed[] = "{" . $item . "}";
                }
            }
        }

        $template = implode(" ", $allowed);
        $buttons = [
            'view' => function ($url, $model) {
                return Html::a("<span class='glyphicon glyphicon-eye-open'></span>", ['view', 'id' => $model->primaryKey], [
                    'title' => 'Lihat Data',
                    'class' => 'open-modal-btn',
                    'data-pjax' => 0,
                ]);
            },
            'update' => function ($url, $model) {
                if ($model->hasAttribute('status')) {
                    if ($model->status != Wfgroup::getMaxStatus()) {
                        if (Yii::$app->controller->id == 'salesorderinfo') {
                            if (Salesorderinfo::getActionGrid($model->salesorderid)) {
                                return Html::a("<span class='glyphicon glyphicon-pencil'></span>", ['update', 'id' => $model->primaryKey], [
                                    'title' => 'Ubah',
                                    'class' => 'open-modal-btn',
                                    'data-pjax' => 0,
                                ]);
                            }
                        } else {
                            return Html::a("<span class='glyphicon glyphicon-pencil'></span>", ['update', 'id' => $model->primaryKey], [
                                'title' => 'Ubah',
                                'class' => 'open-modal-btn',
                                'data-pjax' => 0,
                            ]);
                        }
                    }
                }
            },
            'change' => function ($url, $model) {
                return Html::a("<span class='glyphicon glyphicon-refresh'></span>", ['change', 'id' => $model->primaryKey], [
                    'title' => 'Ubah Password',
                    'class' => 'open-modal-btn'
                ]);
            },
            'delete' => function ($url, $model) {
                if ($model->hasAttribute('status')) {
                    if ($model->status == 1) {
                        $url = ['delete', 'id' => $model->primaryKey];
                        $icon = 'trash';
                        $label = 'Hapus';
                        $confirm = 'Apakah anda yakin akan menghapus data ini?';
                    } elseif ($model->status == 0) {
                        $url = ['restore', 'id' => $model->primaryKey];
                        $icon = 'repeat';
                        $label = 'Batal Hapus';
                        $confirm = 'Apakah anda yakin akan mengaktivasi data ini?';
                    }
                } else {
                    $url = ['delete', 'id' => $model->primaryKey];
                    $icon = 'trash';
                    $label = 'Hapus';
                    $confirm = 'Apakah anda yakin akan menghapus data ini?';
                }
                
                if (Yii::$app->controller->id == 'salesorderinfo') {
                    if (Salesorderinfo::getActionGrid($model->salesorderid)) {
                        return Html::a("<span class='glyphicon glyphicon-$icon'></span>", $url, [
                            'title' => $label,
                            'aria-label' => $label,
                            'data-confirm' => $confirm,
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ]);
                    }
                } else {
                    return Html::a("<span class='glyphicon glyphicon-$icon'></span>", $url, [
                        'title' => $label,
                        'aria-label' => $label,
                        'data-confirm' => $confirm,
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]);
                }
            },
            'browse' => function ($url, $model) {
                if (Yii::$app->controller->id == 'stockopname') {
                    $url = 'create';
                } else {
                    $url = 'generate';
                }
                return Html::a("<span class='glyphicon glyphicon-check'></span>", [$url, 'qq' => $model->primaryKey], [
                    'title' => 'Pilih',
                    'class' => 'open-modal-btn',
                    'data-pjax' => '0'
                ]);
            },
            'approval' => function ($url, $model) {
                if ($model->hasAttribute('status')) {
                    if ($model->status != Wfgroup::getMaxStatus()) {
                        return Html::a("<span class='glyphicon glyphicon-ok'></span>", ['approval', 'id' => $model->primaryKey], [
                            'title' => 'Setuju',
                            'class' => 'open-modal-btn',
                            'data-pjax' => '0',
                            'data-confirm' => 'Apakah anda ingin menyetujui data ini?'
                        ]);
                    }
                }
            },
            'reject' => function ($url, $model) {
                if ($model->hasAttribute('status')) {
                    if ($model->status != Wfgroup::getMaxStatus() && $model->status != Wfgroup::getMinStatus()) {
                        return Html::a("<span class='glyphicon glyphicon-remove'></span>", ['reject', 'id' => $model->primaryKey], [
                            'title' => 'Tolak',
                            'class' => 'open-modal-btn',
                            'data-pjax' => '0',
                            'data-confirm' => 'Apakah anda ingin menolak data ini?'
                        ]);
                    }
                }
            }
        ];
            
        if ($customButtons) {
            $buttons = array_merge($buttons, $customButtons);
        }

        $result = [
            'width' => '11%',
            'class' => 'kartik\grid\ActionColumn',
            'template' => $template,
            'hAlign' => 'center',
            'vAlign' => 'middle',
            'header' => '',
            'buttons' => $buttons
        ];

        if ($columnOptions) {
            $result = array_merge($result, $columnOptions);
        }

        return $result;
    }
    
    public static function getSimpleActionGrid() {
        return [
            'class' => 'kartik\grid\ActionColumn',
            'template' => '{view} {update} {delete}',
            'hAlign' => 'center',
            'vAlign' => 'middle',
            'header' => '',
            'width' => '100px',
            'buttons' => [
                'view' => function ($url, $model) {
                    if (AccessHelper::hasAccess("isread")) {
                        return Html::a("<span class='glyphicon glyphicon-eye-open'></span>", ['view', 'id' => $model->primaryKey], [
                            'title' => 'Lihat Data',
                            'class' => 'open-modal-btn'
                        ]);
                    }
                },
                'update' => function ($url, $model) {
                    if (AccessHelper::hasAccess("iswrite")) {
                        return Html::a("<span class='glyphicon glyphicon-pencil'></span>", ['update', 'id' => $model->primaryKey], [
                            'title' => 'Ubah',
                            'class' => 'open-modal-btn',
                            'data-pjax' => 0,
                        ]);
                    }
                },
                'delete' => function ($url, $model) {
                    if (AccessHelper::hasAccess("ispurge")) {
                        $url = ['delete', 'id' => $model->primaryKey];
                        $icon = 'trash';
                        $label = 'Hapus';
                        $confirm = 'Apakah anda yakin akan menghapus data ini?';
                        return Html::a("<span class='glyphicon glyphicon-$icon'></span>", $url, [
                            'title' => $label,
                            'aria-label' => $label,
                            'data-confirm' => $confirm,
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ]);
                    }
                }
            ]
        ];
    }

    public static function compareDetailAttribute($beforeValue, $afterValue, $columnKey) {
        if (count($beforeValue) != count($afterValue)) {
            return false;
        } else {
            foreach ($afterValue as $array => $arrayContent) {
                if (array_key_exists($array, $beforeValue)) {
                    foreach ($arrayContent as $key => $value) {
                        if (array_key_exists($array[$key], $beforeValue)) {
                            if ($afterValue[$array][$key] != $beforeValue[$array][$key]) {
                                return false;
                            }
                        } else {
                            if ($afterValue[$array][$columnKey] == "") {
                                break;
                            } else {
                                return false;
                            }
                        }
                    }
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    public static function getToolbarCreateButton($title) {
        return AppHelper::getToolbarButton("create", ["create"], $title, "btn-add btn-primary", "plus");
    }
    
    public static function getToolbarBrowseButton($title) {
        return AppHelper::getToolbarButton("create", ["browse"], $title, "btn-add btn-primary", "plus");
    }

    public static function getToolbarDeleteButton($title) {
        return AppHelper::getToolbarButton("data", ["data"], $title, "btn-primary", "trash");
    }

    public static function getToolbarRestoreButton($title) {
        return AppHelper::getToolbarButton("data", ["data"], $title, "btn-primary", "transfer");
    }
    
    public static function getToolbarUploadButton($title, $id) {
        return AppHelper::getToolbarButton("upload", "#", $title, "btn-add btn-success", "upload", NULL, $id);
    }

    public static function getToolbarButton($access, $url, $title, $btnColor, $icon, $text = null, $id = null, $rawUrl = null) {
        if (AccessHelper::hasAccess($access)) {
            return Html::a("<i class='glyphicon glyphicon-$icon'></i>", $url,
                    [
                    'id' => $id,
                    'type' => 'button',
                    'data-pjax' => '0',
                    'title' => $title,
                    'data-raw-url' => Url::to([$rawUrl]),
                    'class' => "btn $btnColor open-modal-btn"
                ]) . ' ';
        } else {
            return "";
        }
    }

    public static function getToolbarFilterButton($model, $text, $title, $modalId = "#search-modal") {
        $isFiltered = false;
        if ($model->getDirtyAttributes()) {
            $modelName = StringHelper::basename(get_class($model));
            $filterModelData = Yii::$app->request->get($modelName);
            if ($filterModelData) {
                foreach ($filterModelData as $key => $value) {
                    if ($value != null) {
                        $isFiltered = true;
                    }
                }
            }
        }
        if ($isFiltered) {
            $filterBadge = '<span class="filter-badge label label-danger">&#9728</span>';
        } else {
            $filterBadge = "";
        }

        return Html::a('<i class="glyphicon glyphicon-filter with-text"></i>' . $text . ' ' . $filterBadge, '#', [
                    'title' => Yii::t('app', 'Filter') . ' ' . $title,
                    'class' => 'btn btn-primary',
                    'data-toggle' => 'modal',
                    'data-target' => $modalId
        ]);
    }

    public static function getToolbarApprovalFilterButton($model, $text, $title, $modalId = "#search-modal", $badge = false) {
        $isFiltered = false;
        if ($badge == true) {
            $filterBadge = '<span class="filter-badge label label-danger">&#9728</span>';
        } else {
            $filterBadge = "";
        }

        return Html::a('<i class="glyphicon glyphicon-filter with-text"></i>' . $text . ' ' . $filterBadge, '#', [
                    'title' => Yii::t('app', 'Filter') . ' ' . $title,
                    'class' => 'btn btn-primary',
                    'data-toggle' => 'modal',
                    'data-target' => $modalId
        ]);
    }
    
    public static function getPrintButton($id) {
        if (AccessHelper::hasAccess('print')) {
            return Html::a("<i class='glyphicon glyphicon-print'></i> Cetak", ['print', 'id' => $id], 
                ['class' => "btn btn-primary", 'target' => '_blank']);
        } else {
            return "";
        }
    }

    public static function getDatePickerRangeConfig($startAttr = 'dateFrom', $endAttr = 'dateTo', $additional = []) {
        $config = [
            'convertFormat' => true,
            'startAttribute' => $startAttr,
            'endAttribute' => $endAttr,
            'pluginOptions' => [
                'autoUpdateInput' => false,
                'locale' => ['format' => 'd-m-Y', 'cancelLabel' => 'Clear'],
            ],
            'pluginEvents' => [
                "cancel.daterangepicker" => "function() { $(this).val('').trigger('change'); }",
                "apply.daterangepicker" => "function(ev, picker) { $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY')); }",
            ]
        ];

        $config = array_merge($config, $additional);

        return $config;
    }
    
    public static function saveAudit($beforeValue, $afterValue, $user_id, $controller, $action, $id) {

        $_modelAudit = new AuditTrail();

        $_modelAudit->user_id = $user_id;
        $_modelAudit->controller = $controller;
        $_modelAudit->action = $action;
        $_modelAudit->created_date = new Expression('NOW()');

        if ($action == 'update') {
            $_modelAudit->beforeValue = $beforeValue;
            $_modelAudit->afterValue = $afterValue;
        } elseif ($action == 'delete') {
            $_modelAudit->beforeValue = $beforeValue;
            $_modelAudit->afterValue = NULL;
        } elseif ($action == 'restore') {
            $_modelAudit->beforeValue = NULL;
            $_modelAudit->afterValue = $afterValue;
        } else {
            $_modelAudit->beforeValue = $beforeValue;
            $_modelAudit->afterValue = $afterValue;
        }
        $_modelAudit->save();
    }

    public static function indonesian_date($timestamp = '', $date_format = 'j F Y', $suffix = '') {

        if (trim($timestamp) == '') {
            $timestamp = time();
        } elseif (!ctype_digit($timestamp)) {
            $timestamp = strtotime($timestamp);
        }

        $date_format = preg_replace("/S/", "", $date_format);
        $pattern = array(
            '/Mon[^day]/', '/Tue[^sday]/', '/Wed[^nesday]/', '/Thu[^rsday]/',
            '/Fri[^day]/', '/Sat[^urday]/', '/Sun[^day]/', '/Monday/', '/Tuesday/',
            '/Wednesday/', '/Thursday/', '/Friday/', '/Saturday/', '/Sunday/',
            '/Jan[^uary]/', '/Feb[^ruary]/', '/Mar[^ch]/', '/Apr[^il]/', '/May/',
            '/Jun[^e]/', '/Jul[^y]/', '/Aug[^ust]/', '/Sep[^tember]/', '/Oct[^ober]/',
            '/Nov[^ember]/', '/Dec[^ember]/', '/January/', '/February/', '/March/',
            '/April/', '/June/', '/July/', '/August/', '/September/', '/October/',
            '/November/', '/December/',
        );
        $replace = array('Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min',
            'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu',
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des',
            'Januari', 'Februari', 'Maret', 'April', 'Juni', 'Juli', 'Agustus', 'Sepember',
            'Oktober', 'November', 'Desember',
        );
        $date = date($date_format, $timestamp);
        $date = preg_replace($pattern, $replace, $date);
        $date = "{$date} {$suffix}";
        return $date;
    }

    public static function to_word($number) {
        $words = "";
        $arr_number = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        if ($number < 12) {
            $words = " " . $arr_number[$number];
        } elseif ($number < 20) {
            $words = AppHelper::to_word($number - 10) . " belas";
        } elseif ($number < 100) {
            $words = AppHelper::to_word($number / 10) . " puluh " . AppHelper::to_word($number % 10);
        } elseif ($number < 200) {
            $words = "seratus " . AppHelper::to_word($number - 100);
        } elseif ($number < 1000) {
            $words = AppHelper::to_word($number / 100) . " ratus " . AppHelper::to_word($number % 100);
        } elseif ($number < 2000) {
            $words = "seribu " . to_word($number - 1000);
        } elseif ($number < 1000000) {
            $words = AppHelper::to_word($number / 1000) . " ribu " . AppHelper::to_word($number % 1000);
        } elseif ($number < 1000000000) {
            $words = to_word($number / 1000000) . " juta " . to_word($number % 1000000);
        } else {
            $words = "undefined";
        }
        return $words;
    }

    public static function getlabel($model) {
        $attr = $model->attributes;
        $label = $model->attributeLabels();
        $attrval = [];
        foreach ($attr as $key => $value) {
            $attrval[] = $value;
        }
        $labelname = [];
        foreach ($label as $key => $value) {
            $labelname[] = $value;
        }
        $result = array_combine($labelname, $attrval);

        return $result;
    }

    public static function saveLog($action, $refNum = "") {
        $model = new Log();
        $model->action = $action;
        $model->refNum = $refNum;
        if ($model->save()) {
            return true;
        } else {
            return false;
        }
    }

    public static function getDsnAttribute($name, $dsn) {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }

}
        