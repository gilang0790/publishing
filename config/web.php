<?php
use kartik\mpdf\Pdf;
use kartik\datecontrol\Module;

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

require __DIR__ . '/container.php';

$config = [
    'id' => 'gilang_ps',
    'name' => "Gilang System",
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'id',
    'timeZone' => 'Asia/Jakarta',
    'modules' => [
        'gridview' => [
			'class' => 'kartik\grid\Module'
		],
        'datecontrol' => [
            'class' => '\kartik\datecontrol\Module',
            'displaySettings' => [
                Module::FORMAT_DATE => 'dd-MM-yyyy',
                Module::FORMAT_TIME => 'hh:mm:ss a',
                Module::FORMAT_DATETIME => 'dd-MM-yyyy HH:mm',
            ],
            'saveSettings' => [
                Module::FORMAT_DATE => 'php:Y-m-d',
                Module::FORMAT_TIME => 'php:H:i:s',
                Module::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
            ],
            'autoWidgetSettings' => [
                Module::FORMAT_DATE => ['type' => 3, 'pluginOptions' => ['autoclose' => true]],
                Module::FORMAT_DATETIME => [], // setup if needed
                Module::FORMAT_TIME => [], // setup if needed
            ],
        ],
        'admin' => [
            'class' => 'app\modules\admin\AdminModule',
        ],
        'common' => [
            'class' => 'app\modules\common\CommonModule',
        ],
        'accounting' => [
            'class' => 'app\modules\accounting\AccountingModule',
        ],
        'inventory' => [
            'class' => 'app\modules\inventory\InventoryModule',
        ],
        'order' => [
            'class' => 'app\modules\order\OrderModule',
        ],
        'purchase' => [
            'class' => 'app\modules\purchase\PurchaseModule',
        ],
        'royalty' => [
            'class' => 'app\modules\royalty\RoyaltyModule',
        ]
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => '0483a2a36ed95c78e2d8dfd35af88435',
            'csrfParam' => '_csrf-skaerp',
        ],
        'session' => [
            'name' => 'skaerp_session',
        ],
        'assetManager' => [
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-blue-light',
                ],
            ],
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource'
                ],
            ],
        ],
        'pdf' => [
            'class' => Pdf::classname(),
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'mode' => Pdf::MODE_UTF8
        // refer settings section for all configuration options
        ],
        'pdf_landscape' => [
            'class' => Pdf::classname(),
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'destination' => Pdf::DEST_BROWSER,
            'mode' => Pdf::MODE_UTF8
        // refer settings section for all configuration options
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\modules\admin\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
        'generators' => [
            'crud' => [
                'class' => 'yii\gii\generators\crud\Generator',
                'templates' => [
                    'adminlte' => '@app/gii_generator/crud/default',
                ]
            ],
        ]
    ];
}

return $config;
