<?php

namespace app\components;

use \Yii;
use yii\web\UnauthorizedHttpException;
use yii\web\Controller;
use app\components\AccessHelper;

class BaseController extends Controller {
    public function init() {
        parent::init();

        if (Yii::$app->user->isGuest) {
            $this->goHome();
        } else {
            $username = Yii::$app->user->identity->username;
            $session = Yii::$app->session;
        }
        
        $session = Yii::$app->session;
        $language = $session->get('language') != null ? $session->get('language') : 'id';
        Yii::$app->language = $language;
    }

    public function beforeAction($action) {
        $hasAccess = false;

        if (!Yii::$app->user->isGuest) {
            $hasAccess = AccessHelper::hasAccess();
        }

        if ($hasAccess) {
            return true;
        } else {
            throw new UnauthorizedHttpException();
        }
    }
}