<?php

namespace app\modules\inventory\models\forms;

use Exception;
use Yii;
use app\modules\inventory\models\Stock;
use app\modules\inventory\models\Stockcard;
use app\modules\inventory\models\Goodsissue;
use app\modules\inventory\models\Goodsissuedetail;
use app\modules\common\models\Product;
use app\modules\order\models\Salesorderdetail;
use app\modules\admin\models\Wfgroup;

/**
 * This is the model class for approval goods issue.
 */
class GoodsissueApprove extends Goodsissue
{
    
    public function approve(&$errMsg) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $nextStatus = Wfgroup::getNextStatus('appgi', $this->status);
            $maxStatus = Wfgroup::getMaxStatus('appgi');
            if ($nextStatus) {
                if (!$this->slocid) {
                    $errMsg = 'Gudang harus diisi. Silahkan ubah data terlebih dahulu.';
                    throw new Exception($errMsg);
                }
                $this->status = $nextStatus;
                $this->updatedAt = date('Y-m-d H:i:s');
                $this->updatedBy = Yii::$app->user->identity->userID;
                if (!$this->save()) {
                    $errMsg = 'Kesalahan saat ubah status data.';
                    throw new Exception($errMsg);
                }
                
                $detailModel = Goodsissuedetail::find()->where(['head_id' => $this->id])->all();
                if ($detailModel) {
                    foreach ($detailModel as $data) {
                        $productname = Product::getProductName($data->productid);
                        if ($data->qty > $data->soqty) {
                            $errMsg = "Jumlah pengeluaran barang $productname melebihi jumlah penjualan.";
                            throw new Exception($errMsg);
                        }
                    }
                }
                
                if ($maxStatus == $nextStatus) {
                    if ($detailModel) {
                        $stockID = NULL;
                        foreach ($detailModel as $data) {
                            // Cek stok barang
                            $stock = Stock::getStock($this->slocid, $data->storagebinid, $data->productid);
                            $productname = Product::getProductName($data->productid);
                            if ($stock < $data->qty) {
                                $errMsg = "Stok $productname tidak mencukupi.";
                                throw new Exception($errMsg);
                            }
                            $stockModel = Stock::find()
                                ->andWhere([
                                    'productid' => $data->productid,
                                    'unitofmeasureid' => $data->unitofmeasureid,
                                    'slocid' => $this->slocid,
                                    'storagebinid' => $data->storagebinid,
                                ])
                                ->one();
                            if ($stockModel) {
                                $stockID = $stockModel->stockid;
                                $totalQty = $stockModel->qty - $data->qty;
                                if ($totalQty < 0) {
                                    $errMsg = 'Jumlah barang tidak boleh minus.';
                                    throw new Exception($errMsg);
                                } else {
                                    $stockModel->qty = $stockModel->qty - $data->qty;
                                }
                                $stockModel->updatedAt = date('Y-m-d H:i:s');
                                $stockModel->updatedBy = Yii::$app->user->identity->userID;
                                if (!$stockModel->save()) {
                                    $errMsg = 'Gagal ubah stok barang.';
                                    throw new Exception($errMsg);
                                }
                            }
                            
                            $stockCardModel = new Stockcard();
                            $stockCardModel->stockid = $stockID;
                            $stockCardModel->productid = $data->productid;
                            $stockCardModel->unitofmeasureid = $data->unitofmeasureid;
                            $stockCardModel->slocid = $this->slocid;
                            $stockCardModel->storagebinid = $data->storagebinid;
                            $stockCardModel->transdate = $this->gitransdate;
                            $stockCardModel->refnum = $this->gitransnum;
                            $stockCardModel->qtyout = $data->qty;
                            $stockCardModel->transtype = Yii::$app->controller->id;
                            $stockCardModel->createdAt = date('Y-m-d H:i:s');
                            $stockCardModel->createdBy = Yii::$app->user->identity->userID;
                            if (!$stockCardModel->save()) {
                                $errMsg = 'Gagal simpan kartu stok.';
                                throw new Exception($errMsg);
                            }
                            
                            $salesOrderDetailModel = Salesorderdetail::findOne($data->salesorderdetailid);
                            if ($salesOrderDetailModel) {
                                $salesOrderDetailModel->giqty = $data->qty;
                                if (!$salesOrderDetailModel->save()) {
                                    $errMsg = 'Gagal ubah jumlah pengeluaran barang di data penjualan.';
                                    throw new Exception($errMsg);
                                }
                            }
                        }
                    }
                }
            } else {
                $errMsg = 'Anda belum memiliki akses untuk proses data pengeluaran barang. Silahkan ke menu Alur Kerja dan Grup.';
                throw new Exception($errMsg);
            }
            
            $transaction->commit();
            return true;
        } catch (Exception $ex) {
            $transaction->rollBack();
            $errMsg = $ex->getMessage();
            return false;
        }
    }
}
