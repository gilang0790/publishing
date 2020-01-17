<?php

namespace app\modules\inventory\models\forms;

use Exception;
use Yii;
use app\modules\inventory\models\Stock;
use app\modules\inventory\models\Stockcard;
use app\modules\inventory\models\Goodsreceipt;
use app\modules\inventory\models\Goodsreceiptdetail;
use app\modules\common\models\Product;
use app\modules\purchase\models\Purchaseorderdetail;
use app\modules\admin\models\Wfgroup;

/**
 * This is the model class for approval goods receipt.
 */
class GoodsreceiptApprove extends Goodsreceipt
{
    
    public function approve(&$errMsg) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $nextStatus = Wfgroup::getNextStatus('appgr', $this->status);
            $maxStatus = Wfgroup::getMaxStatus('appgr');
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
                
                $detailModel = Goodsreceiptdetail::find()
                    ->innerJoinWith("product")
                    ->where(['ms_product.type' => Product::STOCK, 'tr_goodsreceiptdetail.head_id' => $this->id])->all();
                
                // @Notes: Cek jumlah penerimaan dengan pembelian
                if ($detailModel) {
                    foreach ($detailModel as $data) {
                        $productname = Product::getProductName($data->productid);
                        if (($data->qty + $data->podetail->grqty) > $data->poqty) {
                            $errMsg = "Jumlah penerimaan barang $productname melebihi jumlah pembelian.";
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
                                $stockModel->hpp = $data->podetail->price;
                                $stockModel->buyprice = $data->podetail->price;
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
                                $newStockModel->hpp = $data->podetail->price;
                                $newStockModel->buyprice = $data->podetail->price;
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
                            $stockCardModel->transdate = $this->grtransdate;
                            $stockCardModel->refnum = $this->grtransnum;
                            $stockCardModel->qtyin = $data->qty;
                            $stockCardModel->transtype = Yii::$app->controller->id;
                            $stockCardModel->hpp = $data->podetail->price;
                            $stockCardModel->buyprice = $data->podetail->price;
                            $stockCardModel->createdAt = date('Y-m-d H:i:s');
                            $stockCardModel->createdBy = Yii::$app->user->identity->userID;
                            if (!$stockCardModel->save()) {
                                $errMsg = 'Gagal simpan kartu stok.';
                                throw new Exception($errMsg);
                            }
                            
                            $purchaseOrderDetailModel = Purchaseorderdetail::findOne($data->purchaseorderdetailid);
                            if ($purchaseOrderDetailModel) {
                                $purchaseOrderDetailModel->grqty = $data->qty + $purchaseOrderDetailModel->grqty;
                                if (!$purchaseOrderDetailModel->save()) {
                                    $errMsg = 'Gagal ubah jumlah penerimaan barang di data pembelian.';
                                    throw new Exception($errMsg);
                                }
                            }
                        }
                    }
                }
            } else {
                $errMsg = 'Anda belum memiliki akses untuk proses data penerimaan barang. Silahkan ke menu Alur Kerja dan Grup.';
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
