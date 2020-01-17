<?php

use kartik\checkbox\CheckboxX;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\admin\models\search\MenuaccessSearchModel;
use app\modules\admin\models\search\GroupaccessSearchModel;
use app\modules\admin\models\search\GroupmenuSearchModel;
?>

<div class="box box-primary">
    <?php
    $form = ActiveForm::begin(['id' => 'theForm', 'enableAjaxValidation' => true]); ?>
    <div class="box-header with-border">
        <h3 class="box-title">
            Informasi <?= $this->title ?>
        </h3>
    </div>
    <div class="box-body">
        <div class='row'>
            <div class='col-md-12'>
                <?=
                $form->field($model, 'groupaccessid')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(GroupaccessSearchModel::findActive()->orderBy('groupaccessid')->all(),
                            'groupaccessid', 'groupname'),
                    'options' => [
                        'prompt' => '--- Pilih Grup ---'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>
        </div>
        <div class="row">
            <div class='col-md-12'>
                <table class="table table-bordered group-menu-table" style="border-collapse:inherit;">
                    <thead>
                        <tr>
                            <th class="text-center btn-info" style="width:50%;">Menu</th>
                            <th class="text-center btn-info" style="width:10%;">Baca</th>
                            <th class="text-center btn-info" style="width:10%;">Tulis</th>
                            <th class="text-center btn-info" style="width:10%;">Setuju</th>
                            <th class="text-center btn-info" style="width:10%;">Tolak</th>
                            <th class="text-center btn-info" style="width:10%;">Unggah</th>
                            <th class="text-center btn-info" style="width:10%;">Unduh</th>
                            <th class="text-center btn-info" style="width:10%;">Hapus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $menuList = MenuaccessSearchModel::getMenuList();
                        if ($menuList) {
                            $j = 0;
                            foreach ($menuList as $menu) {
                                $submenuList = MenuaccessSearchModel::getSubmenuList($menu['moduleid']);
                                echo '<tr class="text-center btn-warning">';
                                echo '  <td colspan="8">';
                                echo        $menu['moduledesc'];
                                echo '  </td>';
                                echo '</tr>';
                                if ($submenuList) {
                                    foreach ($submenuList as $submenu) {
                                        echo '<tr>';
                                        echo '  <td>';
                                        echo '      <input type="hidden" class="menuGroup" name="Groupmenu[menuaccessid]['.$j.']" value="'.$submenu['menuaccessid'].'">';
                                        echo        $submenu['description'];
                                        echo '  </td>';
                                        echo '  <td class="text-center" style="vertical-align: middle;">';
                                        echo        CheckboxX::widget([
                                                        'name' => "Groupmenu[isread][$j]",
                                                        'value' => GroupmenuSearchModel::getValue($model->groupaccessid, $submenu['menuaccessid'], 'isread'),
                                                        'initInputType' => CheckboxX::INPUT_CHECKBOX
                                                    ]); 
                                        echo '  </td>';
                                        echo '  <td class="text-center" style="vertical-align: middle;">';
                                        echo        CheckboxX::widget([
                                                        'name' => "Groupmenu[iswrite][$j]",
                                                        'value' => GroupmenuSearchModel::getValue($model->groupaccessid, $submenu['menuaccessid'], 'iswrite'),
                                                        'initInputType' => CheckboxX::INPUT_CHECKBOX
                                                    ]); 
                                        echo '  </td>';
                                        echo '  <td class="text-center" style="vertical-align: middle;">';
                                        echo        CheckboxX::widget([
                                                        'name' => "Groupmenu[ispost][$j]",
                                                        'value' => GroupmenuSearchModel::getValue($model->groupaccessid, $submenu['menuaccessid'], 'ispost'),
                                                        'initInputType' => CheckboxX::INPUT_CHECKBOX
                                                    ]); 
                                        echo '  </td>';
                                        echo '  <td class="text-center" style="vertical-align: middle;">';
                                        echo        CheckboxX::widget([
                                                        'name' => "Groupmenu[isreject][$j]",
                                                        'value' => GroupmenuSearchModel::getValue($model->groupaccessid, $submenu['menuaccessid'], 'isreject'),
                                                        'initInputType' => CheckboxX::INPUT_CHECKBOX
                                                    ]); 
                                        echo '  </td>';
                                        echo '  <td class="text-center" style="vertical-align: middle;">';
                                        echo        CheckboxX::widget([
                                                        'name' => "Groupmenu[isupload][$j]",
                                                        'value' => GroupmenuSearchModel::getValue($model->groupaccessid, $submenu['menuaccessid'], 'isupload'),
                                                        'initInputType' => CheckboxX::INPUT_CHECKBOX
                                                    ]); 
                                        echo '  </td>';
                                        echo '  <td class="text-center" style="vertical-align: middle;">';
                                        echo        CheckboxX::widget([
                                                        'name' => "Groupmenu[isdownload][$j]",
                                                        'value' => GroupmenuSearchModel::getValue($model->groupaccessid, $submenu['menuaccessid'], 'isdownload'),
                                                        'initInputType' => CheckboxX::INPUT_CHECKBOX
                                                    ]); 
                                        echo '  </td>';
                                        echo '  <td class="text-center" style="vertical-align: middle;">';
                                        echo        CheckboxX::widget([
                                                        'name' => "Groupmenu[ispurge][$j]",
                                                        'value' => GroupmenuSearchModel::getValue($model->groupaccessid, $submenu['menuaccessid'], 'ispurge'),
                                                        'initInputType' => CheckboxX::INPUT_CHECKBOX
                                                    ]); 
                                        echo '  </td>';
                                        echo '</tr>';
                                        $j += 1;
                                    }
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="box-footer text-right">
        <?php if (!isset($isView)): ?>
        <?= Html::submitButton('<i class="glyphicon glyphicon-floppy-disk"></i> Simpan', 
        ['class' => 'btn btn-primary btnSave']) ?>
        <?php endif; ?>
        <?= Html::a('<i class="glyphicon glyphicon-chevron-left"></i> Batal', ['index'], ['class'=>'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>