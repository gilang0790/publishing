<?php
use kartik\grid\GridView;

Yii::$container->set('kartik\grid\GridView', [
    'pjax' => true,
    'pjaxSettings' => [
        'options' => [
            'enablePushState' => false
        ]
    ],
    'export' => false,
]);