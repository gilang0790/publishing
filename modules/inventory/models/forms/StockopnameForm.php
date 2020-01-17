<?php

namespace app\modules\inventory\models\forms;

use Exception;
use Yii;
use app\components\AppHelper;
use app\modules\common\models\search\ProductSearchModel;
use app\modules\inventory\models\Stockopname;
use app\modules\inventory\models\Stockopnamedetail;
use app\modules\admin\models\Wfgroup;

/**
 * This is the model class for upload product.
 *
 * @property string $fileUpload
 */
class StockopnameForm extends Stockopname
{
    
    public function saveModel(&$errMsg, $details, $deletedIDs=null) {
        $transaction = Yii::$app->db->beginTransaction();
        $newTrans = $this->isNewRecord;
        try {
            if ($newTrans) {
                $status = Wfgroup::getMaxStatus('insbs');
                if (!$status) {
                    $errMsg = 'Anda belum memiliki akses untuk buat/ubah data penyesuaian stok. Silahkan ke menu Alur Kerja dan Grup.';
                    throw new Exception($errMsg);
                }
                $newTransNum = AppHelper::createNewTransactionNumber('Stock Opname', $this->bstransdate);

                if ($newTransNum == "") {
                    $errMsg = 'Kesalahan saat membentuk nomor dokumen.';
                    throw new Exception($errMsg);
                }

                $this->bstransnum = $newTransNum;
                $this->status = Wfgroup::getMaxStatus('insbs');
            }
            $finalTotal = str_replace(".", "", $this->total);
            $this->total = $finalTotal;
            
            if (!$this->save(false)) {
                $errMsg = 'Kesalahan saat simpan data.';
                throw new Exception($errMsg);
            }
            
            if (!$newTrans) {
                if (!empty($deletedIDs)) {
                    Stockopnamedetail::deleteAll(['id' => $deletedIDs]);
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
            Yii::error($ex);
            $transaction->rollBack();
            return false;
        }
    }
}
