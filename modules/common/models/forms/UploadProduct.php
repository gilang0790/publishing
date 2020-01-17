<?php

namespace app\modules\common\models\forms;

use Yii;
use app\modules\common\models\Category;
use app\modules\common\models\Product;
use app\modules\common\models\Unitofmeasure;
use PHPExcel_IOFactory;

/**
 * This is the model class for upload product.
 *
 * @property string $fileUpload
 */
class UploadProduct extends Product
{
    public $fileUpload;
    
    public function saveUpload($inputFileName, &$counter, &$errMsg) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);

            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $lastProductID = 0;
            $lastProductName = '';
            
            for ($row = 2; $row <= $highestRow; ++$row) {
                $errVal = '';
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

                if ($rowData[0][1] != '') {
                    $productName = (!is_null($rowData[0][1]) ? trim($rowData[0][1]) : '');
                    if (strlen($productName) > 100) {
                        $errVal .= 'Nama Barang maksimal 100 karakter, ';
                    } else {
                        if ($lastProductName != $productName) {
                            if ($this->checkProductName($productName)) {
                                $errVal .= 'Nama Barang sudah ada di database, ';
                            }
                        }
                    }
                    
                    $productCode = strval(!is_null($rowData[0][2]) ? trim($rowData[0][2]) : "");
                    $categoryID = -1;
                    $categoryName = (!is_null($rowData[0][3]) ? trim($rowData[0][3]) : "");
                    if (strlen($categoryName) > 50) {
                        $errVal .= 'Kategori maksimal 50 karakter, ';
                    } else {
                        $categoryID = $this->getCategoryID($categoryName);
                    }

                    if ($categoryID == -1) {
                        $errVal .= 'Kategori tidak terdaftar di database, ';
                    }
                    
                    $uomID = -1;
                    $uomCode = (!is_null($rowData[0][4]) ? trim($rowData[0][4]) : "");
                    if (strlen($uomCode) > 10) {
                        $errVal .= 'Unit maksimal 10 karakter, ';
                    } else {
                        $uomID = $this->getUnitID($uomCode);
                    }

                    if ($uomID == -1) {
                        $errVal .= 'Unit tidak terdaftar di database, ';
                    }
                    
                    $author = strval(!is_null($rowData[0][5]) ? trim($rowData[0][5]) : "");
                    $isbn = strval(!is_null($rowData[0][6]) ? trim($rowData[0][6]) : "");
                    
                    $typeID = -1;
                    $typeName = strval(!is_null($rowData[0][7]) ? trim($rowData[0][7]) : "");
                    if (strlen($typeName) < 1 || !in_array($typeName, ['Barang', 'Jasa'])) {
                        $errVal .= 'Tipe wajib diisi: Barang atau Jasa, ';
                    } else {
                        $typeID = $typeName == 'Barang' ? 1 : 2;
                    }
                    if ($typeID == -1) {
                        $errVal .= "Tipe Barang $typeName tidak terdaftar di database, ";
                    }
                    
                    $size = strval(!is_null($rowData[0][8]) ? trim($rowData[0][8]) : "");
                    $weight = strval(!is_null($rowData[0][9]) ? trim($rowData[0][9]) : "");
                    $notes = strval(!is_null($rowData[0][10]) ? $rowData[0][10] : "");
                    if (strlen($notes) > 500) {
                        $errVal .= 'Catatan maksimal 500 karakter, ';
                    }
                    
                    if ($errVal == '') {
                        if ($lastProductName != $productName) {
                            $productModel = new Product();
                            $productModel->productname = $productName;
                            $productModel->productcode = $productCode;
                            $productModel->categoryid = $categoryID;
                            $productModel->unitofmeasureid = $uomID;
                            $productModel->isbn = $isbn;
                            $productModel->type = $typeID;
                            $productModel->author = $author;
                            $productModel->size = $size;
                            $productModel->weight = $weight;
                            $productModel->notes = $notes;
                            
                            if ($productModel->save()) {
                                $lastProductID = $productModel->productid;
                            } else {
                                if ($errMsg == '') {
                                    $cRow = $row;
                                    $errMsg = '- Excel Row ' . $cRow . ' ';
                                }
                                $errMsg .= $productModel->getErrors();
                            }
                        }
                        
                        $lastProductName = $productName;
                        $counter += 1;
                    } else {
                        $errVal = substr($errVal, 0, -2);
                        $cRow = $row;
                        $errMsg .= '- Excel Row ' . $cRow . " : " . $errVal . "<br>";
                    }
                } else {
                    break;
                }
            }
            
            if ($errMsg == '') {
                $transaction->commit();
            } else {
                $transaction->rollback();
            }
        } catch (Exception $ex) {
            $transaction->rollback();
            $errMsg = $ex->getMessage();
        }
        
        unlink($inputFileName);
        return true;
    }
    
    private function checkProductName($productName) {
        $exists = false;
        $model = Product::findOne([
                'productname' => $productName,
                'status' => 1,
        ]);

        if (isset($model)) {
            $exists = true;
        }

        return $exists;
    }
    
    public static function getCategoryID($category) {
        $categoryID = -1;
        $model = Category::findOne([
                'categoryname' => $category
        ]);

        if (isset($model)) {
            $categoryID = $model->categoryid;
        }

        return $categoryID;
    }
    
    public static function getUnitID($uomCode) {
        $uomID = -1;
        $model = Unitofmeasure::findOne([
                'uomcode' => $uomCode
        ]);

        if (isset($model)) {
            $uomID = $model->unitofmeasureid;
        }

        return $uomID;
    }
}
