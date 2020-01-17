<?php

namespace app\modules\accounting\models\forms;

use Exception;
use Yii;
use app\components\AppHelper;
use app\modules\accounting\models\Invoiceap;
use app\modules\accounting\models\Invoiceapdetail;
use app\modules\inventory\models\Goodsreceipt;
use app\modules\inventory\models\Goodsreceiptdetail;
use app\modules\purchase\models\Purchaseorderdetail;
use app\modules\admin\models\Wfgroup;

class InvoiceapGenerate extends Invoiceap
{
    public function generate(&$errMsg, $qq) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $status = Wfgroup::getMaxStatus('insap');
            $goodsreceiptModel = Goodsreceipt::findOne($qq);
            if ($status) {
                if ($goodsreceiptModel) {
                    $newTransNum = AppHelper::createNewTransactionNumber('Account Payable', date('Y-m-d'));
                    if ($newTransNum == "") {
                        $errMsg = 'Kesalahan saat membentuk nomor dokumen.';
                        throw new Exception($errMsg);
                    }

                    // simpan data head invoice ap
                    $this->aptransnum = $newTransNum;
                    $this->aptransdate = date('Y-m-d');
                    $this->plantid = $goodsreceiptModel->purchaseorder->plantid;
                    $this->addressbookid = $goodsreceiptModel->purchaseorder->addressbookid;
                    $this->apamount = $this->getApamount($goodsreceiptModel->id);
                    $this->payamount = 0;
                    $this->goodsreceiptid = $goodsreceiptModel->id;
                    $this->headernote = $goodsreceiptModel->headernote;
                    $this->status = $status;
                    $this->createdAt = date('Y-m-d H:i:s');
                    $this->createdBy = Yii::$app->user->identity->userID;
                    if (!$this->save(false)) {
                        $errMsg = 'Kesalahan saat simpan data.';
                        throw new Exception($errMsg);
                    }

                    // Ubah isgenerated menjadi true
                    $goodsreceiptModel->isgenerated = 1;
                    if (!$goodsreceiptModel->save(false)) {
                        $errMsg = 'Kesalahan saat simpan data penerimaan barang.';
                        throw new Exception($errMsg);
                    }

                    // simpan invoice ap detail
                    $details = $this->findModelDetail($goodsreceiptModel->id);
                    if ($details) {
                        foreach ($details as $detail) {
                            $actualQty = $detail->qty - $detail->invqty - $detail->retqty;
                            $ardetailModel = new Invoiceapdetail();
                            $ardetailModel->head_id = $this->id;
                            $ardetailModel->productid = $detail->productid;
                            $ardetailModel->unitofmeasureid = $detail->unitofmeasureid;
                            $ardetailModel->qty = $actualQty;
                            $ardetailModel->price = $detail->podetail->price;
                            $ardetailModel->total = $actualQty * $detail->podetail->price;
                            $ardetailModel->goodsreceiptdetailid = $detail->id;
                            $ardetailModel->poqty = $detail->poqty;
                            $ardetailModel->grqty = $detail->qty;
                            if (!$ardetailModel->save(false)) {
                                $errMsg = 'Kesalahan saat simpan detail transaksi.';
                                throw new Exception($errMsg);
                            }
                        }
                    } else {
                        $errMsg = 'Detail transaksi penerimaan barang gagal diakses.';
                        throw new Exception($errMsg);
                    }
                } else {
                    $errMsg = 'Dokumen penerimaan barang gagal diakses.';
                    throw new Exception($errMsg);
                }
            } else {
                $errMsg = 'Anda belum memiliki akses untuk proses data faktur pembelian. Silahkan ke menu Alur Kerja dan Grup.';
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
    
    protected function getApamount($goodsreceiptid) {
        $result = 0;
        $grModel = Goodsreceiptdetail::find()->where(['head_id' => $goodsreceiptid])->all();
        if ($grModel) {
            foreach ($grModel as $data) {
                $total = ($data->qty - $data->invqty - $data->retqty) * $data->podetail->price;
                $result += $total;
            }
        }
        
        return $result;
    }

    protected function findModelDetail($id) {
        $listOutstanding = Goodsreceiptdetail::getOutstandingDetail();
        if (($model = Goodsreceiptdetail::find()->where("id IN $listOutstanding AND head_id = :head_id", [':head_id' => $id])->all()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }

    protected function findPoModelDetail($id, $productid) {
        if (($model = Purchaseorderdetail::find()->where(['head_id' => $id, 'productid' => $productid])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
}
