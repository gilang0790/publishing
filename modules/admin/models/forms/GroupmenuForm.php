<?php

namespace app\modules\admin\models\forms;

use Yii;
use yii\db\Exception;
use app\modules\admin\models\Groupmenu;

/**
 * This is the model class for table "ms_groupmenu".
 *
 * @property int $groupmenuid
 * @property int $groupaccessid
 * @property int $menuaccessid
 * @property int $isread
 * @property int $iswrite
 * @property int $ispost
 * @property int $isreject
 * @property int $isupload
 * @property int $isdownload
 * @property int $ispurge
 *
 * @property Groupaccess $groupaccess
 * @property Menuaccess $menuaccess
 */
class GroupmenuForm extends Groupmenu
{
    public $dataLists;
    /**
     * {@inheritdoc}
     */
    public function saveModel()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $dataArray = $this->dataLists;
            if ($dataArray['GroupmenuForm']) {
                $groupMenu = $dataArray['Groupmenu'];
                $groupaccessid = $dataArray['GroupmenuForm']['groupaccessid'];
                $menuaccessid = $groupMenu['menuaccessid'];
                for($i=0;$i<count($menuaccessid);$i++) {
                    $groupmenuModel = NULL;
                    if ($this->findGroupmenuModel($groupaccessid, $menuaccessid[$i])) {
                        // Jika update data
                        $groupmenuModel = $this->findGroupmenuModel($groupaccessid, $menuaccessid[$i]);
//                        if ($menuaccessid[$i] == '33') {
//                        echo '<pre>';
//                        var_dump($groupmenuModel);
//                        echo '</pre>';die();
//                        }
                        $groupmenuModel->groupaccessid = $groupaccessid;
                        $groupmenuModel->menuaccessid = $menuaccessid[$i];
                        $groupmenuModel->isread = isset($groupMenu['isread'][$i]) == '1' ? '1' : '0';
                        $groupmenuModel->iswrite = isset($groupMenu['iswrite'][$i]) == '1' ? '1' : '0';
                        $groupmenuModel->ispost = isset($groupMenu['ispost'][$i]) == '1' ? '1' : '0';
                        $groupmenuModel->isreject = isset($groupMenu['isreject'][$i]) == '1' ? '1' : '0';
                        $groupmenuModel->isupload = isset($groupMenu['isupload'][$i]) == '1' ? '1' : '0';
                        $groupmenuModel->isdownload = isset($groupMenu['isdownload'][$i]) == '1' ? '1' : '0';
                        $groupmenuModel->ispurge = isset($groupMenu['ispurge'][$i]) == '1' ? '1' : '0';
                        if (!$groupmenuModel->save()) {
                            $transaction->rollBack();
                            return false;
                        }
                    } else {
                        // Jika create new
                        if (isset($groupMenu['isread'][$i]) == '1') {
                            $groupmenuModel = new Groupmenu();
                            $groupmenuModel->groupaccessid = $groupaccessid;
                            $groupmenuModel->menuaccessid = $menuaccessid[$i];
                            $groupmenuModel->isread = isset($groupMenu['isread'][$i]) == '1' ? '1' : '0';
                            $groupmenuModel->iswrite = isset($groupMenu['iswrite'][$i]) == '1' ? '1' : '0';
                            $groupmenuModel->ispost = isset($groupMenu['ispost'][$i]) == '1' ? '1' : '0';
                            $groupmenuModel->isreject = isset($groupMenu['isreject'][$i]) == '1' ? '1' : '0';
                            $groupmenuModel->isupload = isset($groupMenu['isupload'][$i]) == '1' ? '1' : '0';
                            $groupmenuModel->isdownload = isset($groupMenu['isdownload'][$i]) == '1' ? '1' : '0';
                            $groupmenuModel->ispurge = isset($groupMenu['ispurge'][$i]) == '1' ? '1' : '0';
                            if (!$groupmenuModel->save()) {
                                $transaction->rollBack();
                                return false;
                            }
                        }
                    }
                }
                $transaction->commit();
                return true;
            }
        } catch (Exception $ex) {
            $transaction->rollBack();
            return false;
        }
    }
}
