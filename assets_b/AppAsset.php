<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets_b;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web/assets_b';
    public $css = [
        'css/fonts/Roboto/roboto.css',
        'css/morris.css',
        'css/ionicons.min.css',
        'css/flag-icon.min.css',
        'css/jquery.dataTables.min.css',
        'themes/areas.css',
        'themes/month_white.css',
        'themes/month_green.css',
        'themes/month_transparent.css',
        'themes/month_traditional.css',
        'themes/navigator_8.css',
        'themes/navigator_white.css',
        'css/site.css'
    ];
    public $js = [
        'js/bootbox.min.js',
        'js/jquery.maskMoney.js',
        'js/daypilot-all.min.js',
        'js/jquery.knob.js',
        'js/morris.js',
        'js/autoNumeric.min.js',
        'js/jquery.dirrty.js',
        'js/raphael-min.js',
        'js/moment.js',
        'js/js_browse.js',
        'js/jquery.floatThead.min.js',
        'js/jquery.blockUI.js',
        'js/jquery.dataTables.min.js',
        'js/interact.js',
        'js/Chart.bundle.min.js',
        'js/chartjs-plugin-datalabels.min.js',
        'js/vue.js',
        'js/vue-custom-components.js',
        'js/js_general.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        //'yii\jui\JuiAsset',
        'yii\web\JqueryAsset',
        '\rmrevin\yii\fontawesome\AssetBundle',
        '\kartik\select2\Select2Asset',
        'yii\widgets\MaskedInputAsset'
    ];
}