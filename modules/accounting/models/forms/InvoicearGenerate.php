<?php

namespace app\modules\accounting\models\forms;

use Exception;
use Yii;
use app\components\AppHelper;
use app\modules\accounting\models\Invoicear;
use app\modules\accounting\models\Invoiceardetail;
use app\modules\inventory\models\Goodsissue;
use app\modules\inventory\models\Goodsissuedetail;
use app\modules\order\models\Salesorderdetail;
use app\modules\admin\models\Wfgroup;


class InvoicearGenerate extends Invoicear
{
    
    public function generate(&$errMsg, $qq) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $status = Wfgroup::getMaxStatus('insar');
            $goodsissueModel = Goodsissue::findOne($qq);
            if ($status) {
                if ($goodsissueModel) {
                    $newTransNum = AppHelper::createNewTransactionNumber('Account Receivable', date('Y-m-d'));
                    if ($newTransNum == "") {
                        $errMsg = 'Kesalahan saat membentuk nomor dokumen.';
                        throw new Exception($errMsg);
                    }

                    // simpan data head invoice ar
                    $this->artransnum = $newTransNum;
                    $this->artransdate = date('Y-m-d');
                    $this->plantid = $goodsissueModel->salesorder->plantid;
                    $this->addressbookid = $goodsissueModel->salesorder->addressbookid;
                    $this->paymentmethodid = $goodsissueModel->salesorder->paymentmethodid;
                    $this->dueDate = $goodsissueModel->salesorder->dueDate;
                    $this->shippingcost = $goodsissueModel->shippingcost;
                    $this->grandtotal = $goodsissueModel->salesorder->grandtotal;
                    $this->aramount = $this->getAramount($goodsissueModel->id);
                    $this->payamount = 0;
                    $this->goodsissueid = $goodsissueModel->id;
                    $this->headernote = $goodsissueModel->headernote;
                    $this->status = $status;
                    $this->createdAt = date('Y-m-d H:i:s');
                    $this->createdBy = Yii::$app->user->identity->userID;
                    if (!$this->save(false)) {
                        $errMsg = 'Kesalahan saat simpan data.';
                        throw new Exception($errMsg);
                    }

                    // Ubah isgenerated menjadi true
                    $goodsissueModel->isgenerated = 1;
                    if (!$goodsissueModel->save(false)) {
                        $errMsg = 'Kesalahan saat simpan data pengeluaran barang.';
                        throw new Exception($errMsg);
                    }

                    // simpan invoice ar detail
                    $details = $this->findModelDetail($goodsissueModel->id);
                    if ($details) {
                        foreach ($details as $detail) {
                            $actualQty = $detail->qty - $detail->invqty - $detail->retqty;
                            $ardetailModel = new Invoiceardetail();
                            $ardetailModel->head_id = $this->id;
                            $ardetailModel->productid = $detail->productid;
                            $ardetailModel->unitofmeasureid = $detail->unitofmeasureid;
                            $ardetailModel->qty = $actualQty;
                            $ardetailModel->addressbookid = $detail->goodsissue->salesorder->addressbookid;
                            $ardetailModel->price = $detail->sodetail->price;
                            $ardetailModel->vat = $detail->sodetail->vat;
                            $ardetailModel->discount = $detail->sodetail->discount;
                            $ardetailModel->totalvat = $actualQty * $detail->sodetail->price * $detail->sodetail->vat / 100;
                            $ardetailModel->totaldiscount = $actualQty * $detail->sodetail->price * $detail->sodetail->discount / 100;
                            $ardetailModel->total = $this->getDetailTotal($actualQty, $detail->sodetail->price, $detail->sodetail->vat, $detail->sodetail->discount);
                            $ardetailModel->goodsissuedetailid = $detail->id;
                            $ardetailModel->soqty = $detail->soqty;
                            $ardetailModel->giqty = $detail->qty;
                            if (!$ardetailModel->save(false)) {
                                $errMsg = 'Kesalahan saat simpan detail transaksi.';
                                throw new Exception($errMsg);
                            }
                        }
                    } else {
                        $errMsg = 'Detail transaksi pengeluaran barang gagal diakses.';
                        throw new Exception($errMsg);
                    }
                } else {
                    $errMsg = 'Dokumen pengeluaran barang gagal diakses.';
                    throw new Exception($errMsg);
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
    
    protected function getAramount($goodsissueid) {
        $result = 0;
        $giModel = Goodsissuedetail::find()->where(['head_id' => $goodsissueid])->all();
        if ($giModel) {
            foreach ($giModel as $data) {
                $total = (($data->qty - $data->invqty - $data->retqty) * $data->sodetail->price) +
                            ((($data->qty - $data->invqty - $data->retqty) * $data->sodetail->price) * $data->sodetail->vat / 100) - 
                            ((($data->qty - $data->invqty - $data->retqty) * $data->sodetail->price) * $data->sodetail->discount / 100);
                $result += $total;
            }
        }
        
        return $result;
    }
    
    protected function getDetailTotal($qty, $price, $vat, $discount) {
        $result = ($qty * $price) +
                    ($qty * $price * $vat / 100) - 
                    ($qty * $price * $discount / 100);
        
        return $result;
    }

    protected function findModelDetail($id) {
        $listOutstanding = Goodsissuedetail::getOutstandingDetail();
        if (($model = Goodsissuedetail::find()->where("id IN $listOutstanding AND head_id = :head_id", [':head_id' => $id])->all()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }

    protected function findSoModelDetail($id, $productid) {
        if (($model = Salesorderdetail::find()->where(['head_id' => $id, 'productid' => $productid])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang dituju tidak diketahui.');
    }
}
