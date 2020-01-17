<?php

namespace app\modules\inventory\models\forms;

use Yii;
use app\components\AppHelper;
use app\modules\common\models\Product;
use app\modules\inventory\models\Stockopname;
use app\modules\inventory\models\Stockopnamedetail;
use app\modules\common\models\search\SlocSearchModel;
use app\modules\common\models\search\ProductSearchModel;
use app\modules\common\models\search\StoragebinSearchModel;
use app\modules\admin\models\Wfgroup;
use PHPExcel_IOFactory;

/**
 * This is the model class for upload product.
 *
 * @property string $fileUpload
 */
class UploadStockopname extends Stockopname
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
            $lastSlocID = 0;
            $lastProductName = '';
            $saveHead = FALSE;
            
            for ($row = 2; $row <= $highestRow; ++$row) {
                $errVal = '';
                $grandTotal = 0;
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

                if ($rowData[0][1] != '') {
                    $status = Wfgroup::getMaxStatus('insbs');
                    if (!$status) {
                        $errVal .= 'Anda belum memiliki akses untuk buat/ubah data penyesuaian stok. Silahkan ke menu Alur Kerja dan Grup.';
                    }
                    
                    $productName = (!is_null($rowData[0][1]) ? $rowData[0][1] : '');
                    $productModel = Product::find()->where(['like', 'productname', $productName])->one();
                    $productID = $productModel ? $productModel->productid : 0;
                    if ($productID == 0) {
                        $errVal .= 'Barang tidak terdaftar';
                    }
                    $qty = (!is_null($rowData[0][2]) ? $rowData[0][2] : 0);
                    $sloccode = (!is_null($rowData[0][3]) ? $rowData[0][3] : '');
                    $slocModel = SlocSearchModel::findActive()->where(['like', 'sloccode', $sloccode])->one();
                    $slocID = $slocModel ? $slocModel->slocid : 0;
                    if ($slocID == 0) {
                        $errVal .= 'Gudang tidak terdaftar';
                    }
                    if ($saveHead) {
                        if ($lastSlocID != $slocID) {
                            $errVal .= 'Gudang harus sama tiap barisnya';
                        }
                    }
                    $storagebinDesc = (!is_null($rowData[0][4]) ? $rowData[0][4] : '');
                    $storagebinModel = StoragebinSearchModel::findActive()
                        ->andWhere(['slocid' => $slocID])
                        ->andWhere(['like', 'description', $storagebinDesc])->one();
                    $storagebinID = $storagebinModel ? $storagebinModel->storagebinid : 0;
                    if ($storagebinID == 0) {
                        $errVal .= 'Rak tidak terdaftar';
                    }
                    $hpp = (!is_null($rowData[0][5]) ? $rowData[0][5] : 0);
                    if ($hpp == 0) {
                        $errVal .= 'HPP harus diisi';
                    }
                    $type = NULL;
                    $typedesc = (!is_null($rowData[0][6]) ? $rowData[0][6] : 0);
                    if (strtolower($typedesc) == 'tambah') {
                        $type = 'plus';
                    } elseif (strtolower($typedesc) == 'kurang') {
                        $type = 'minus';
                    } elseif ($typedesc == 0) {
                        $errVal .= 'Tipe harus diisi tambah atau kurang';
                    }
                    $itemTotal = (!is_null($rowData[0][7]) ? $rowData[0][7] : 0);
                    
                    if ($errVal == '') {
                        if ($lastProductName != $productName) {
                            if (!$saveHead) {
                                $lastSlocID = $slocID;
                                $newTransNum = AppHelper::createNewTransactionNumber('Stock Opname', date('Y-m-d'));
                                if ($newTransNum == "") {
                                    $transaction->rollBack();
                                    return false;
                                }
                                $stockOpnameModel = new Stockopname();
                                $stockOpnameModel->bstransnum = $newTransNum;
                                $stockOpnameModel->bstransdate = date('Y-m-d');
                                $stockOpnameModel->slocid = $slocID;
                                $stockOpnameModel->status = $status;
                                $stockOpnameModel->createdBy = Yii::$app->user->identity->userID;
                                $stockOpnameModel->createdAt = date('Y-m-d H:i:s');
                                if (!$stockOpnameModel->save(false)) {
                                    throw new Exception();
                                }
                                $saveHead = TRUE;
                            }
                            
                            $detailModel = new Stockopnamedetail();
                            $detailModel->head_id = $stockOpnameModel->id;
                            $detailModel->productid = $productID;
                            $detailModel->unitofmeasureid = ProductSearchModel::getUomID($productID);
                            $detailModel->qty = $qty;
                            $detailModel->storagebinid = $storagebinID;
                            $detailModel->hpp = $hpp;
                            $detailModel->type = $type;
                            $detailModel->total = $qty*$hpp;
                            $grandTotal += $detailModel->total;
                            
                            if (!$detailModel->save()) {
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
                    $updateModel = Stockopname::findOne($stockOpnameModel->id);
                    $updateModel->total = $grandTotal;
                    if (!$stockOpnameModel->save(false)) {
                        throw new Exception();
                    }
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
}
