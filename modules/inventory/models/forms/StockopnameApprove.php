<?php

namespace app\modules\inventory\models\forms;

use Exception;
use Yii;
use app\modules\inventory\models\Stock;
use app\modules\inventory\models\Stockcard;
use app\modules\inventory\models\Stockopname;
use app\modules\inventory\models\Stockopnamedetail;
use app\modules\admin\models\Wfgroup;

/**
 * This is the model class for upload product.
 *
 * @property string $fileUpload
 */
class StockopnameApprove extends Stockopname
{
    
    public function approve(&$errMsg) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $nextStatus = Wfgroup::getNextStatus('appbs', $this->status);
            $maxStatus = Wfgroup::getMaxStatus('appbs');
            if ($nextStatus) {
                $this->status = $nextStatus;
                $this->updatedAt = date('Y-m-d H:i:s');
                $this->updatedBy = Yii::$app->user->identity->userID;
                if (!$this->save()) {
                    $errMsg = 'Kesalahan saat ubah status data.';
                    throw new Exception($errMsg);
                }
                
                if ($maxStatus == $nextStatus) {
                    $detailModel = Stockopnamedetail::find()->where(['head_id' => $this->id])->all();
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
                                if ($data->type == 'plus') {
                                    $stockModel->qty = $stockModel->qty + $data->qty;
                                } elseif ($data->type == 'minus') {
                                    $totalQty = $stockModel->qty - $data->qty;
                                    if ($totalQty < 0) {
                                        $errMsg = 'Jumlah barang tidak boleh minus.';
                                        throw new Exception($errMsg);
                                    } else {
                                        $stockModel->qty = $stockModel->qty - $data->qty;
                                    }
                                }
                                $stockModel->hpp = $data->hpp;
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
                                $newStockModel->hpp = $data->hpp;
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
                            $stockCardModel->transdate = $this->bstransdate;
                            $stockCardModel->refnum = $this->bstransnum;
                            if ($data->type == 'plus') {
                                $stockCardModel->qtyin = $data->qty;
                            } elseif ($data->type == 'minus') {
                                $stockCardModel->qtyout = $data->qty;
                            }
                            $stockCardModel->transtype = Yii::$app->controller->id;
                            $stockCardModel->hpp = $data->hpp;
                            $stockCardModel->createdAt = date('Y-m-d H:i:s');
                            $stockCardModel->createdBy = Yii::$app->user->identity->userID;
                            if (!$stockCardModel->save()) {
                                $errMsg = 'Gagal simpan kartu stok.';
                                throw new Exception($errMsg);
                            }
                        }
                    }
                }
            } else {
                $errMsg = 'Anda belum memiliki akses untuk proses data penyesuaian stok. Silahkan ke menu Alur Kerja dan Grup.';
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
