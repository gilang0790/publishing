<?php

namespace app\components;

use Yii;
use \Exception;

class AccessHelper {
    
    public static function hasAccess($action=null, $controller=null) {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        if (!$action) {
            $action = Yii::$app->controller->action->id;
        }
        if ($action == 'index' || $action == 'view') {
            $action = 'isread';
        } elseif ($action == 'create' || $action == 'update' || $action == 'change' || 
            $action == 'browse' || $action == 'generate' || $action == 'lists') {
            $action = 'iswrite';
        } elseif ($action == 'approval') {
            $action = 'ispost';
        } elseif ($action == 'reject') {
            $action = 'isreject';
        } elseif ($action == 'upload') {
            $action = 'isupload';
        } elseif ($action == 'download' || $action == 'print') {
            $action = 'isdownload';
        } elseif ($action == 'delete' || $action == 'restore') {
            $action = 'ispurge';
        }
        if (!$controller) {
            $controller = Yii::$app->controller->id;
        }
        $hasAccess = FALSE;
        $db = Yii::$app->db;
        $sql = "select ".$action." as akses ".
		" from ms_user a ".
		" inner join ms_usergroup b on b.userID = a.userID ".
		" inner join ms_groupmenu c on c.groupaccessid = b.groupaccessid ".
		" inner join ms_menuaccess d on d.menuaccessid = c.menuaccessid ".
		" where lower(username) = '".Yii::$app->user->identity->username."' and lower(menuname) = '".$controller."' limit 1";
        $createCommand = $db->createCommand($sql);
        $cmd = $createCommand->queryOne();
        if ($cmd['akses'] == 1)
        {
            $hasAccess = TRUE;
        }
        return $hasAccess;
    }

}