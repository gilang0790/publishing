<?php
use app\components\AppHelper;
use app\modules\admin\models\Wfstatus;

$salesType = AppHelper::$salesType;
$status = Wfstatus::getStatusArray();
?>
<!DOCTYPE html>
<html lang="id">
    <head>
    </head>
    <body>
        <div style="margin-top: 10px;">
            <div style="font-size: 10px;"><i>Perusahaan :</i></div>
            <div><strong><?= $model->sloc->plant->company->companyname ?></strong></div>
        </div>
        <div style="font-size: 12px">
            <div style="margin-top: 10px; text-align: center; font-size: 16px;"><b>Retur Penjualan</b></div>
            <div style="margin-top: 20px;">
                <div style="float: left; width: 50%;">
                    <div style="display: block; margin-left: -3px;">
                        <table style="width: 100%; font-size: 12px;">
                            <tr>
                                <td style="width: 100px; font-weight: bold;"><?= $model->getAttributeLabel('slocid') ?></td>
                                <td style="width: 15px;">:</td>
                                <td><?= $model->sloc->description ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;"><?= $model->getAttributeLabel('goodsissueid') ?></td>
                                <td>:</td>
                                <td><?= $model->goodsissue->gitransnum ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div style="float: left;">
                    <div style="display: block; margin-left: 50px;">
                        <table style="width: 100%; font-size: 12px;">
                            <tr>
                                <td style="width: 150px; font-weight: bold;"><?= $model->getAttributeLabel('girtransnum') ?></td>
                                <td style="width: 15px;">:</td>
                                <td><?= $model->girtransnum ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;"><?= $model->getAttributeLabel('girtransdate') ?></td>
                                <td>:</td>
                                <td><?= date_format(date_create($model->girtransdate), 'd/M/Y') ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div style="margin-top: 30px;">
                <div style="display: block;">
                    <table style="width: 100%; margin-left: -3px; font-size: 12px; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th style="width: 5%; padding-bottom: 5px; text-align: center; border-bottom:1px solid #000;">No.</th>
                                <th style="width: 30%; padding-bottom: 5px; text-align: center; border-bottom:1px solid #000">Barang</th>
                                <th style="width: 10%; padding-bottom: 5px; text-align: center; border-bottom:1px solid #000">Satuan</th>
                                <th style="width: 10%; padding-bottom: 5px; text-align: right; border-bottom:1px solid #000">Jumlah</th>
                                <th style="width: 15%; padding-bottom: 5px; text-align: center; border-bottom:1px solid #000">Rak</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            foreach ($details as $detail) {
                            ?>
                            <tr style="margin: 5px 1%; ">
                                <td style="text-align: center; padding-top: 6px; padding-bottom: 6px; border-bottom:1px solid #000; "><?= $i ?></td>
                                <td style="padding-top: 6px; padding-bottom: 6px; border-bottom:1px solid #000;"><?= $detail->product->productname ?>&nbsp;</td>
                                <td style="padding-top: 6px; padding-bottom: 6px; border-bottom:1px solid #000; text-align: center"><?= $detail->unit->uomcode ?></td>
                                <td style="text-align: right; padding-top: 6px; padding-bottom: 6px; border-bottom:1px solid #000; "><?= number_format($detail->qty, 2, ",", ".") ?></td>
                                <td style="text-align: center; padding-top: 6px; padding-bottom: 6px; border-bottom:1px solid #000; "><?= $detail->storagebin->description ?></td>
                            </tr>
                            <?php
                                $i = $i + 1;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <br>
                <br>
                <br>
                <div>
                    <div style="width: 150px; float: left; font-weight: bold;"><?= $model->getAttributeLabel('headernote')?></div>
                    <div style="display:block; float: left;">: <?= $model->headernote ?></div>
                    <div style="width: 150px; float: left; font-weight: bold;"><?= $model->getAttributeLabel('status')?></div>
                    <div style="display:block; float: left;">: <?= $status[$model->status]?></div>
                    <div style="width: 150px; float: left; font-weight: bold;">Tanggal Cetak</div>
                    <div style="display:block; float: left;">: <?= date('d/M/Y H:i:s') ?></div>
                </div>
                <br>
                <table style="width: 100%; margin-left: -3px; font-size: 8px; border-collapse: collapse; ">
                    <tbody>
                        <tr style="margin: 5px 1%; ">
                            <td style="text-align: left; padding-top: 0px; width: 8%; font-weight: bold;"><?= $model->getAttributeLabel('createdBy')?></td>
                            <td style="text-align: left; width: 20%;">: <?= $model->createdby->username ?></td>
                            <td style="text-align: left; padding-top: 0px; width: 8%; font-weight: bold;"><?= $model->getAttributeLabel('updatedBy')?></td>
                            <td style="text-align: left; width: 20%;">: <?= $model->updatedby->username ?></td>
                            <td style="text-align: left; padding-top: 0px; width: 8%; font-weight: bold;">Disetujui Oleh</td>
                            <td style="text-align: left; width: 20%;">: <?= $model->updatedby->username ?></td>
                        </tr>
                        <tr style="margin: 5px 1%; ">
                            <td style="text-align: left; padding-top: 0px; width: 8%; font-weight: bold;"><?= $model->getAttributeLabel('createdAt')?></td>
                            <td style="text-align: left; width: 20%;">: <?= date("d/m/Y H:i:s", strtotime($model->createdAt)) ?></td>
                            <td style="text-align: left; padding-top: 0px; width: 8%; font-weight: bold;"><?= $model->getAttributeLabel('updatedAt')?></td>
                            <td style="text-align: left; width: 20%;">: <?= date("d/m/Y H:i:s", strtotime($model->updatedAt)) ?></td>
                            <td style="text-align: left; padding-top: 0px; width: 8%; font-weight: bold;">Disetujui Pada</td>
                            <td style="text-align: left; width: 20%;">: <?= date("d/m/Y H:i:s", strtotime($model->updatedAt)) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>



