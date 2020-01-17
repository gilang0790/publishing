<?php

namespace app\modules\order\models\forms;

use Exception;
use Yii;
use app\components\AppHelper;
use app\modules\common\models\search\ProductSearchModel;
use app\modules\order\models\Salesorder;
use app\modules\order\models\Salesorderdetail;
use app\modules\admin\models\Wfgroup;

/**
 * This is the model class for create and update sales order.
 */
class SalesorderForm extends Salesorder
{
    
    public function saveModel(&$errMsg, $details, $deletedIDs=null) {
        $transaction = Yii::$app->db->beginTransaction();
        $newTrans = $this->isNewRecord;
        try {
            if ($newTrans) {
                $status = Wfgroup::getMaxStatus('insso');
                if (!$status) {
                    $errMsg = 'Anda belum memiliki akses untuk buat/ubah data penjualan. Silahkan ke menu Alur Kerja dan Grup.';
                    throw new Exception($errMsg);
                }
                $newTransNum = AppHelper::createNewTransactionNumber('Sales Order', $this->sotransdate);

                if ($newTransNum == "") {
                    $errMsg = 'Kesalahan saat membentuk nomor dokumen.';
                    throw new Exception($errMsg);
                }
                $this->sotransnum = $newTransNum;
                $this->status = $status;
            }
                
            $finalGrandTotal = str_replace(".", "", $this->grandtotal);
            $this->grandtotal = $finalGrandTotal;
            
            if (!$this->save(false)) {
                $errMsg = 'Kesalahan saat simpan data.';
                throw new Exception($errMsg);
            }
            
            if (!$newTrans) {
                if (!empty($deletedIDs)) {
                    Salesorderdetail::deleteAll(['id' => $deletedIDs]);
                }
            }
            
            foreach ($details as $detail) {
                $detail->head_id = $this->id;
                $detail->unitofmeasureid = ProductSearchModel::getUomID($detail->productid);
                if (!$detail->save(false)) {
                    $errMsg = 'Kesalahan saat simpan detail transaksi.';
                    throw new Exception($errMsg);
                }
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
