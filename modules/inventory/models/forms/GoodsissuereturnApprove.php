<?php

namespace app\modules\inventory\models\forms;

use Exception;
use Yii;
use app\modules\inventory\models\Stock;
use app\modules\inventory\models\Stockcard;
use app\modules\inventory\models\Goodsissuereturn;
use app\modules\inventory\models\Goodsissuedetailreturn;
use app\modules\common\models\Product;
use app\modules\inventory\models\Goodsissuedetail;
use app\modules\order\models\Salesorderdetail;
use app\modules\accounting\models\Invoiceardetail;
use app\modules\admin\models\Wfgroup;

/**
 * This is the model class for approval goods issue return.
 */
class GoodsissuereturnApprove extends Goodsissuereturn
{
    
    public function approve(&$errMsg) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $nextStatus = Wfgroup::getNextStatus('appgir', $this->status);
            $maxStatus = Wfgroup::getMaxStatus('appgir');
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
                
                $detailModel = Goodsissuedetailreturn::find()->where(['head_id' => $this->id])->all();
                if ($detailModel) {
                    foreach ($detailModel as $data) {
                        $productname = Product::getProductName($data->productid);
                        if (($data->qty + $data->gidetail->retqty) > $data->giqty) {
                            $errMsg = "Jumlah retur barang $productname melebihi jumlah pengeluaran barang.";
                            throw new Exception($errMsg);
                        }
                    }
                }
                
                if ($maxStatus == $nextStatus) {
                    if ($detailModel) {
                        $stockID = NULL;
                        foreach ($detailModel as $data) {
                            // @Notes: Cek apakah data sudah ada di stok
                            $stockModel = Stock::find()
                                ->andWhere([
                                    'productid' => $data->productid,
                                    'unitofmeasureid' => $data->unitofmeasureid,
                                    'slocid' => $this->slocid,
                                    'storagebinid' => $data->storagebinid,
                                ])->one();
                            if ($stockModel) {
                                $stockID = $stockModel->stockid;
                                $stockModel->qty = $stockModel->qty + $data->qty;
                                $stockModel->updatedAt = date('Y-m-d H:i:s');
                                $stockModel->updatedBy = Yii::$app->user->identity->userID;
                                if (!$stockModel->save()) {
                                    $errMsg = 'Gagal simpan stok eksisting.';
                                    throw new Exception($errMsg);
                                }
                            } else {
                                $newStockModel = new Stock();
                                $newStockModel->productid = $data->productid;
                                $newStockModel->unitofmeasureid = $data->unitofmeasureid;
                                $newStockModel->slocid = $this->slocid;
                                $newStockModel->storagebinid = $data->storagebinid;
                                $newStockModel->qty = $data->qty;
                                $newStockModel->createdAt = date('Y-m-d H:i:s');
                                $newStockModel->createdBy = Yii::$app->user->identity->userID;
                                if (!$newStockModel->save()) {
                                    $errMsg = 'Gagal simpan stok baru.';
                                    throw new Exception($errMsg);
                                }
                                $stockID = $newStockModel->stockid;
                            }
                            
                            $stockCardModel = new Stockcard();
                            $stockCardModel->stockid = $stockID;
                            $stockCardModel->productid = $data->productid;
                            $stockCardModel->unitofmeasureid = $data->unitofmeasureid;
                            $stockCardModel->slocid = $this->slocid;
                            $stockCardModel->storagebinid = $data->storagebinid;
                            $stockCardModel->transdate = $this->girtransdate;
                            $stockCardModel->refnum = $this->girtransnum;
                            $stockCardModel->qtyin = $data->qty;
                            $stockCardModel->transtype = Yii::$app->controller->id;
                            $stockCardModel->createdAt = date('Y-m-d H:i:s');
                            $stockCardModel->createdBy = Yii::$app->user->identity->userID;
                            if (!$stockCardModel->save()) {
                                $errMsg = 'Gagal simpan kartu stok.';
                                throw new Exception($errMsg);
                            }
                            
                            $soDetailModel = Salesorderdetail::findOne($data->gidetail->salesorderdetailid);
                            if ($soDetailModel) {
                                $soDetailModel->retqty = $data->qty + $soDetailModel->retqty;
                                if (!$soDetailModel->save()) {
                                    $errMsg = 'Gagal ubah jumlah di data penjualan.';
                                    throw new Exception($errMsg);
                                }
                            }
                            
                            $goodsIssueDetailModel = Goodsissuedetail::findOne($data->goodsissuedetailid);
                            if ($goodsIssueDetailModel) {
                                $goodsIssueDetailModel->retqty = $data->qty + $goodsIssueDetailModel->retqty;
                                if (!$goodsIssueDetailModel->save()) {
                                    $errMsg = 'Gagal ubah jumlah di data pengeluaran barang.';
                                    throw new Exception($errMsg);
                                }
                            }
                        }
                    }
                }
            } else {
                $errMsg = 'Anda belum memiliki akses untuk proses data retur penjualan. Silahkan ke menu Alur Kerja dan Grup.';
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
