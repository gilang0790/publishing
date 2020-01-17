<?php

namespace app\components;

use app\models\LkPaymentMethodType;
use app\models\LkStatus;
use app\models\MsBranch;
use app\models\MsCategory;
use app\models\MsCurrency;
use app\models\MsCustomer;
use app\models\MsLocation;
use app\models\MsMember;
use app\models\MsMenuCategory;
use app\models\MsMenuCategoryDetail;
use app\models\MsPaymentMethod;
use app\models\MsProduct;
use app\models\MsPurpose;
use app\models\MsSubCategory;
use app\models\MsSupplier;
use app\models\MsTable;
use app\models\MsUom;
use app\models\MsVisitPurpose;
use app\models\Report;
use kartik\grid\GridView;
use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\data\SqlDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\Html;
use yii\helpers\Url;

class ReportEngine {

    public static function getGridView(Model $report, $query, $headers, $title = null, $gridOpts = [], $pageSize = 50) {
        $paginationSetting = $pageSize == 0 ? false : ['pageSize' => $pageSize];

        if (!$title) {
            $title = $report->reportTitle;
        }

        if (!is_array($query)) {
            /* @var $query ActiveQuery */
            $totalCount = $query->count();
            $sql = $query->createCommand()->rawSql;

            $dataProvider = new SqlDataProvider([
                'sql' => $sql,
                'sort' => false,
                'totalCount' => $totalCount,
                'pagination' => $paginationSetting
            ]);
        } else {
            $dataProvider = new ArrayDataProvider([
                'allModels' => $query,
                'sort' => false,
                'pagination' => $paginationSetting
            ]);
        }

        $gridColumns = self::getGridColumns($headers);

        $gridConfig = [
            'pjax' => true,
            'dataProvider' => $dataProvider,
            'toolbar' => false,
            'panel' => [
                'heading' => $title
            ],
            'columns' => $gridColumns
        ];

        $mergedConfig = array_merge($gridConfig, $gridOpts);

        return GridView::widget($mergedConfig);
    }

    public static function downloadReport(Model $report, $query, $columnDefinitions, $title = null) {
        if (!$title) {
            $title = $report->reportTitle;
        }

        $fileName = $title . " - " . date("YmdHis");
        $generatedDate = date('d-m-Y H:i:s');
        $writer = new XLSXWriter();

        $writer->setAuthor("Application");
        $writer->setTitle($title);
        $writer->setDescription("$title gerenerated from Application");

        $headersType = [];
        $headersLabel = [];
        $columnWidths = [];

        foreach ($columnDefinitions as $column) {
            $headersLabel[] = $column['label'];
            $headersType[] = $column['type'];
            if (isset($column['width'])) {
                $columnWidths[] = $column['width'];
            } else {
                $columnWidths[] = '24';
            }
        }

        // Set column type definitions
        $writer->writeSheetHeader('Report', $headersType, [
            'font-style' => 'bold',
            'widths' => $columnWidths,
            'suppress_row' => true
        ]);

        // Write report title
        $writer->writeSheetRowInString('Report', [$title], [
            'font-style' => 'bold',
            'font-size' => 16,
        ]);

        // Write blank space
        $writer->writeSheetRowInString('Report', [], [
            'font-style' => 'bold',
        ]);
        
        $generatedLabel = 'Generated';
        $writer->writeSheetRowInString('Report', [$generatedLabel, $generatedDate], [
                    'font-style' => 'bold',
        ]);

        // Show Filtered Data
        foreach ($report->getAttributes(null, ["reportTitle"]) as $attribute => $value) {            
            if ((!empty($value) || $value === "0") && $attribute != 'dateTo' && $attribute != 'expectedDate' && $attribute != 'expectedDateTo' && $attribute != 'reportDate') {
                $label = (new Report())->getAttributeLabel($attribute);
                $value = self::getAttributeValue($attribute, $value);

                if ($attribute == 'dateFrom') {
                    $label = "Period";
                    $value = $report->dateFrom . " - " . $report->dateTo;
                }

                if ($attribute == 'dateReport') {
                    $value = Yii::$app->formatter->asDate($report->dateReport, 'dd-MM-yyyy');
                }

                if ($attribute == 'expectedDateFrom') {
                    $label = "Expected Date";
                    $value = $report->expectedDateFrom . " - " . $report->expectedDateTo;
                }

                if ($attribute == 'showMenuPackage') {
                    $value = $report->showMenuPackage == 0 ? 'No' : 'Yes';
                }
                
                if ($attribute == 'showDetailCashFlow') {
                    $value = $report->showDetailCashFlow == 0 ? 'No' : 'Yes';
                }

                $writer->writeSheetRowInString('Report', [$label, $value], [
                    'font-style' => 'bold',
                ]);
            }
        }

        // Write blank space
        $writer->writeSheetRowInString('Report', []);

        // Write header
        $writer->writeSheetRowInString('Report', $headersLabel, [
            'font-style' => 'bold',
            'border' => 'left,top,right,bottom',
            'border-style' => 'dashed',
            'halign' => 'center',
        ]);

        $defaultRowDetailStyle = [
            "border" => "left,top,right,bottom",
            "border-style" => 'dashed',
        ];

        if (is_array($query)) {
            foreach ($query as $data) {
                //order query data based on column definitions
                $rowStyle = isset($data['customRowStyle']) ? $data['customRowStyle'] : $defaultRowDetailStyle;
                $intersect = array_intersect_key($data, $columnDefinitions);
                $sortedData = array_replace($columnDefinitions, $intersect);

                $writer->writeSheetRow('Report', $sortedData, $rowStyle);
            }
        } else {
            foreach ($query->each() as $data) {
                //order query data based on column definitions
                $rowStyle = isset($data['customRowStyle']) ? $data['customRowStyle'] : $defaultRowDetailStyle;
                $intersect = array_intersect_key($data, $columnDefinitions);
                $sortedData = array_replace($columnDefinitions, $intersect);

                $writer->writeSheetRow('Report', $sortedData, $rowStyle);
            }
        }
        
        header("Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Content-Disposition:attachment;filename="' . $fileName . '.xlsx"');

        $writer->writeToStdOut();
        Yii::$app->end();
    }

    private static function getAttributeValue($attribute, $value) {
        switch ($attribute) {
            case "branchID":
                if (is_array($value)) {
                    $branchIDs = [];
                    foreach ($value as $id) {
                        $branchIDs[] = MsBranch::findOne($id)->branchName;
                    }
                    $value = implode(", ", $branchIDs);
                } else {
                    $value = MsBranch::findOne($value)->branchName;
                }
                break;
            case "memberID":
                $value = MsMember::findOne($value)->memberName;
                break;
            case "tableID":
                if ($value == 0) {
                    $value = "Take Away";
                } else {
                    $value = MsTable::findOne($value)->tableName;
                }
                break;
            case "paymentMethodID":
                $paymentMethodNames = [];
                foreach ($value as $id) {
                    $paymentMethodNames[] = MsPaymentMethod::findOne($id)->paymentMethodName;
                }
                $value = implode(", ", $paymentMethodNames);
                break;
            case "visitPurposeID":
                $visitPurposeNames = [];
                foreach ($value as $id) {
                    $visitPurposeNames[] = MsVisitPurpose::findOne($id)->visitPurposeName;
                }
                $value = implode(", ", $visitPurposeNames);
                break;
            case "locationID":
                $locationNames = [];
                if (is_array($value)) {
                    foreach ($value as $id) {
                        $locationNames[] = MsLocation::findOne($id)->locationName;
                    }
                    $value = implode(", ", $locationNames);
                } else {
                    $value = MsLocation::findOne($value)->locationName;
                }
                break;
            case "transType":
                $transTypes = [];
                if (is_array($value)) {
                    foreach ($value as $id) {
                        $transTypes[] = $id;
                    }
                    $value = implode(", ", $transTypes);
                } else {
                    $value = $value;
                }
                break;
            case "supplierID":
                $value = MsSupplier::findOne($value)->supplierName;
                break;
            case "customerID":
                $value = MsCustomer::findOne($value)->customerName;
                break;
            case "categoryID":
                $categoryNames = [];
                if (is_array($value)) {
                    foreach ($value as $id) {
                        $categoryNames[] = MsCategory::findOne($id)->categoryName;
                    }
                    $value = implode(", ", $categoryNames);
                } else {
                    $value = MsCategory::findOne($value)->categoryName;
                }

                break;
            case "subCategoryID":
                $subCategoryNames = [];
                if (is_array($value)) {
                    foreach ($value as $id) {
                        $subCategoryNames[] = MsSubCategory::findOne($id)->subCategoryName;
                    }
                    $value = implode(", ", $subCategoryNames);
                } else {
                    $value = MsSubCategory::findOne($value)->subCategoryName;
                }
                break;
            case "uomID":
                $value = MsUom::findOne($value)->uomName;
                break;
            case "currencyID":
                $value = MsCurrency::findOne($value)->currencyName;
                break;
            case "paymentMethodTypeID":
                $value = LkPaymentMethodType::findOne($value)->paymentMethodTypeName;
                break;
            case "menuCategory":
                $value = MsMenuCategory::findOne($value)->menuCategoryDesc;
                break;
            case "menuCategoryDetail":
                $value = MsMenuCategoryDetail::findOne($value)->menuCategoryDetailDesc;
                break;
            case "purposeID":
                $value = MsPurpose::findOne($value)->purposeName;
                break;
            case "status":
                if (is_array($value)) {
                    $statusNames = [];
                    foreach ($value as $id) {
                        $statusNames[] = LkStatus::findOne($id)->statusName;
                    }
                    $value = implode(", ", $statusNames);
                } else {
                    $value = LkStatus::findOne($value)->statusName;
                }
                break;
        }

        return $value;
    }

    private static function getGridColumns($columnDefinitions) {
        $columns = [];
        $columns[] = [
            'class' => 'kartik\grid\SerialColumn',
            'width' => Yii::$app->params['serialColumnWidth'],
        ];
        foreach ($columnDefinitions as $key => $header) {
            $type = $header['type'];
            $label = $header['label'];
            $format = "text";
            $hAlign = "left";
            $headerAlign = "center";
            $columnOptions = isset($header["columnOptions"]) ? $header["columnOptions"] : [];
            $value = isset($header["value"]) ? $header["value"] : '';
            if ($type == "date") {
                $format = ['date', 'php:d/m/Y'];
                $hAlign = "center";
                $headerAlign = "center";
            }

            if ($type == "datetime") {
                $format = ['date', 'php:d/m/Y H:i:s'];
                $hAlign = "center";
                $headerAlign = "center";
            }

            if ($type == "price") {
                $format = ['decimal', 2];
                $hAlign = "right";
                $headerAlign = "right";
            }

            if ($type == "integer") {
                $format = ['decimal', 0];
                $hAlign = "right";
                $headerAlign = "right";
            }
            
            if ($type == "string-time") {
                $hAlign = "center";
                $headerAlign = "center";
            }
            
            if($value == ''){
                $config = [
                    'attribute' => $key,
                    'label' => $label,
                    'format' => $format,
                    'headerOptions' => [
                        'class' => "text-$headerAlign",
                    ],
                    'contentOptions' => [
                        'class' => "text-$hAlign",
                    ],
                ];
            } else {
                $config = [
                    'attribute' => $key,
                    'label' => $label,
                    'format' => 'raw',
                    'headerOptions' => [
                        'class' => "text-$headerAlign",
                    ],
                    'contentOptions' => [
                        'class' => "text-$hAlign",
                    ],
                ];
            }
            
            $mergedConfig = array_merge($config, $columnOptions);
            $columns[] = $mergedConfig;
        }
        return $columns;
    }
    
    public static function getCoaGL($dateFrom, $dateTo, $coaNo) {
        $url = null;
        if (AccessHelper::hasAccess("index", "general-ledger")) {
            $url = Yii::$app->request->baseUrl . '/general-ledger?'
            . 'coaNo='. $coaNo . '&'
            . 'TrJournalDetail%5BjournalDate%5D=' . $dateFrom . '+-+' . $dateTo . '&'
            . 'TrJournalDetail%5BdateFrom%5D=' . $dateFrom . '&'
            . 'TrJournalDetail%5BdateTo%5D=' . $dateTo . '&'
            . 'TrJournalDetail%5BcoaNo%5D='. $coaNo . '&'
            . 'TrJournalDetail%5BtransactionType%5D=&'
            . 'TrJournalDetail%5BrefNum%5D=&'
            . 'TrJournalDetail%5BfilterBranchID%5D=&'
            . 'TrJournalDetail%5BdebitCreditFilter%5D=all&'
            . '_pjax=%23search-pjax';
        }

        if ($url) {
            return Html::a($coaNo, $url,
                    [
                    'target' => '_blank',
                    'data-pjax' => '0',
                    'class' => 'asdasd'
            ]);
        } else {
            return $coaNo;
        }
    }
    
}
