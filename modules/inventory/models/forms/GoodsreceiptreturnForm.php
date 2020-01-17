<?php

namespace app\modules\inventory\models\forms;

use Exception;
use Yii;
use app\modules\common\models\Product;
use app\modules\common\models\search\ProductSearchModel;
use app\modules\inventory\models\Goodsreceiptreturn;
use app\modules\inventory\models\Goodsreceiptdetailreturn;


class GoodsreceiptreturnForm extends Goodsreceiptreturn
{   
    public function saveModel(&$errMsg, $details, $deletedIDs=null) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->updatedAt = date('Y-m-d H:i:s');
            $this->updatedBy = Yii::$app->user->identity->userID;
            if (!$this->save(false)) {
                $errMsg = 'Kesalahan saat simpan data.';
                throw new Exception($errMsg);
            }
            
            if (!empty($deletedIDs)) {
                Goodsreceiptdetailreturn::deleteAll(['id' => $deletedIDs]);
            }

            foreach ($details as $detail) {
                $productname = Product::getProductName($detail->productid);
                if (($detail->qty + $detail->grdetail->retqty) > $detail->grqty) {
                    $errMsg = "Jumlah $productname yang diretur melebihi jumlah penerimaan barang.";
                    throw new Exception($errMsg);
                }
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
