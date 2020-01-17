<?php

use app\assets_b\AppAsset;
use dmstr\web\AdminLteAsset;
use dmstr\helpers\AdminLteHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */

AdminLteAsset::register($this);
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?= Url::to(["assets_b/favico.ico"])?>" rel="shortcut icon">
        <?= Html::csrfMetaTags() ?>
        <title><?= Yii::$app->name ?>  - <?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body class="hold-transition sidebar-mini sidebar-collapse <?= AdminLteHelper::skinClass() ?>">
        <div id="loading-div"></div>
        <?php $this->beginBody() ?>
        <div id="loading-div"></div>
        <div class="wrapper">
            <?php if (!isset($this->params['browse'])): ?>
            <?= $this->render('header.php') ?>

            <?= $this->render('left.php') ?>

            <?= $this->render('content.php', ['content' => $content]) ?>
            <?php endif ?>
        </div>

        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
