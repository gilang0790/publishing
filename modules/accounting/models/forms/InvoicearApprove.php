<?php

namespace app\modules\accounting\models\forms;

use Exception;
use Yii;
use app\modules\inventory\models\Goodsissue;
use app\modules\inventory\models\Goodsissuedetail;
use app\modules\royalty\models\forms\InsertRoyalty;
use app\modules\accounting\models\Invoicear;
use app\modules\accounting\models\Invoiceardetail;
use app\modules\admin\models\Modules;
use app\modules\royalty\models\Royaltysetting;
use app\modules\order\models\Salesorderdetail;
use app\modules\order\models\Salesorderinfo;
use app\modules\order\models\Salesorderinfodetail;
use app\modules\admin\models\Wfgroup;

/**
 * This is the model class for approve sales order.
 */
class InvoicearApprove extends Invoicear
{
    
    public function approve(&$errMsg) {
        $transaction = Yii::$app->db->beginTransaction();
        $goodsissueModel = Goodsissue::findOne($this->goodsissueid);
        try {
            $nextStatus = Wfgroup::getNextStatus('appar', $this->status);
            $maxStatus = Wfgroup::getMaxStatus('appar');
            if ($nextStatus) {
                $this->status = $nextStatus;
                $this->updatedAt = date('Y-m-d H:i:s');
                $this->updatedBy = Yii::$app->user->identity->userID;
                if (!$this->save()) {
                    $errMsg = 'Kesalahan saat ubah status data.';
                    throw new Exception($errMsg);
                }

                if ($maxStatus == $nextStatus) {
                    $checkInfoModel = Salesorderinfo::find()->where(['salesorderid' => $this->goodsissue->salesorderid])->one();
                    $invoiceArDetailModel = $this->findArModelDetail($this->id);
                    $giDetailModel = $this->findGiDetailModel($goodsissueModel->id);
                    $soDetailModel = $this->findSoDetailModel($this->goodsissue->salesorderid);
                    if ($checkInfoModel) {
                        if ($invoiceArDetailModel) {
                            foreach ($invoiceArDetailModel as $detail) {
                                $soInfoDetail = $this->findSoInfoDetailModel($detail->productid, $detail->addressbookid);
                                $soInfoDetail->invqty = $detail->qty;
                                if (!$soInfoDetail->save(false)) {
                                    $errMsg = 'Kesalahan saat ubah detail data info penjualan.';
                                    throw new Exception($errMsg);
                                }
                            }
                        }
                    }
                    if ($giDetailModel) {
                        foreach ($giDetailModel as $detail) {
                            $detail->invqty = $this->getArQty($detail->productid);
                            if (!$detail->save(false)) {
                                $errMsg = 'Kesalahan saat ubah detail data pengeluaran barang.';
                                throw new Exception($errMsg);
                            }
                        }
                    }
                    if ($soDetailModel) {
                        foreach ($soDetailModel as $detail) {
                            $detail->invqty = $this->getArQty($detail->productid);
                            if (!$detail->save(false)) {
                                $errMsg = 'Kesalahan saat ubah detail data penjualan barang.';
                                throw new Exception($errMsg);
                            }
                        }
                    }
                    
                    // Engine royalti penulis
                    if (Modules::getActiveModule("royalty")) {
                        foreach ($invoiceArDetailModel as $detail) {
                            $royaltySettingModel = Royaltysetting::find()
                                ->where([
                                    'productid' => $detail->productid,
                                    'status' => Royaltysetting::STATUS_ACTIVE])
                                ->one();
                            if ($royaltySettingModel) {
                                $insertRoyalty = new InsertRoyalty();
                                $insertRoyalty->productid = $detail->productid;
                                $insertRoyalty->salesorderid = $this->goodsissue->salesorderid;
                                $insertRoyalty->invoicearid = $this->id;
                                $insertRoyalty->royaltysettingid = $royaltySettingModel->royaltysettingid;

                                try {
                                    if (!$insertRoyalty->save()) {
                                        Yii::warning(json_encode($insertRoyalty->getErrors()));
                                        throw new Exception(json_encode($insertRoyalty->getErrors()));
                                    }
                                } catch (Exception $ex) {
                                    $errMsg = 'Kesalahan saat input data royalti penulis.';
                                    throw new Exception($ex->getMessage());
                                }
                            }
                        }
                    }
                }
            } else {
                $errMsg = 'Anda belum memiliki akses untuk proses data faktur penjualan. Silahkan ke menu Alur Kerja dan Grup.';
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
    
    protected function findArModelDetail($id) {
        if (($model = Invoiceardetail::find()->where(['head_id' => $id])->all()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
    
    protected function findGiDetailModel($id) {
        if (($model = Goodsissuedetail::find()->where(['head_id' => $id])->all()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
    
    protected function findSoDetailModel($id) {
        if (($model = Salesorderdetail::find()->where(['head_id' => $id])->all()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
    
    protected function findSoInfoDetailModel($productid, $addressbookid) {
        if (($model = Salesorderinfodetail::find()->where(['productid' => $productid, 'addressbookid' => $addressbookid])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
    
    private function getArQty($productid) {
        $detailModel = Invoiceardetail::find()
            ->select([
                'tr_invoiceardetail.productid',
                'qty' => new \yii\db\Expression("SUM(qty)")
            ])
            ->andWhere(['=', 'tr_invoiceardetail.productid', $productid])
            ->andWhere(['=', 'tr_invoiceardetail.head_id', $this->id])
            ->groupBy('tr_invoiceardetail.productid')
            ->one();
        
        if ($detailModel) {
            return $detailModel['qty'];
        }
        return null;
    }
}
