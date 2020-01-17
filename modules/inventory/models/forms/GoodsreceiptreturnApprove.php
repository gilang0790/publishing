<?php

namespace app\modules\inventory\models\forms;

use Exception;
use Yii;
use app\modules\inventory\models\Stock;
use app\modules\inventory\models\Stockcard;
use app\modules\inventory\models\Goodsreceiptreturn;
use app\modules\inventory\models\Goodsreceiptdetailreturn;
use app\modules\common\models\Product;
use app\modules\inventory\models\Goodsreceiptdetail;
use app\modules\purchase\models\Purchaseorderdetail;
use app\modules\admin\models\Wfgroup;

/**
 * This is the model class for approval goods receipt return.
 */
class GoodsreceiptreturnApprove extends Goodsreceiptreturn
{
    
    public function approve(&$errMsg) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $nextStatus = Wfgroup::getNextStatus('appgrr', $this->status);
            $maxStatus = Wfgroup::getMaxStatus('appgrr');
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
                
                $detailModel = Goodsreceiptdetailreturn::find()->where(['head_id' => $this->id])->all();
                if ($detailModel) {
                    foreach ($detailModel as $data) {
                        $productname = Product::getProductName($data->productid);
                        if (($data->qty + $data->grdetail->retqty) > $data->grqty) {
                            $errMsg = "Jumlah retur barang $productname melebihi jumlah penerimaan barang.";
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
                            $stockCardModel->transdate = $this->grrtransdate;
                            $stockCardModel->refnum = $this->grrtransnum;
                            $stockCardModel->qtyout = $data->qty;
                            $stockCardModel->transtype = Yii::$app->controller->id;
                            $stockCardModel->createdAt = date('Y-m-d H:i:s');
                            $stockCardModel->createdBy = Yii::$app->user->identity->userID;
                            if (!$stockCardModel->save()) {
                                $errMsg = 'Gagal simpan kartu stok.';
                                throw new Exception($errMsg);
                            }
                            
                            $poDetailModel = Purchaseorderdetail::findOne($data->grdetail->purchaseorderdetailid);
                            if ($poDetailModel) {
                                $poDetailModel->retqty = $data->qty + $poDetailModel->retqty;
                                if (!$poDetailModel->save()) {
                                    $errMsg = 'Gagal ubah jumlah di data pembelian.';
                                    throw new Exception($errMsg);
                                }
                            }
                            
                            $goodsReceiptDetailModel = Goodsreceiptdetail::findOne($data->goodsreceiptdetailid);
                            if ($goodsReceiptDetailModel) {
                                $goodsReceiptDetailModel->retqty = $data->qty + $goodsReceiptDetailModel->retqty;
                                if (!$goodsReceiptDetailModel->save()) {
                                    $errMsg = 'Gagal ubah jumlah di data penerimaan barang.';
                                    throw new Exception($errMsg);
                                }
                            }
                        }
                    }
                }
            } else {
                $errMsg = 'Anda belum memiliki akses untuk proses data retur pembelian. Silahkan ke menu Alur Kerja dan Grup.';
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
