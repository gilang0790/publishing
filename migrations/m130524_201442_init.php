<?php

use yii\db\Migration;
use app\models\User;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $transaction = Yii::$app->db->beginTransaction();

        $this->createTable('ms_user', [
            'userID' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'fullName' => $this->string()->notNull(),
            'authKey' => $this->string(32)->notNull(),
            'passwordHash' => $this->string()->notNull(),
            'email' => $this->string()->notNull()->unique(),
            'status' => $this->tinyInteger(1)->notNull()->defaultValue(1),
            'createdAt' => $this->dateTime()->notNull(),
            'updatedAt' => $this->dateTime()->notNull(),
            'createdBy' => $this->integer(),
            'updatedBy' => $this->integer(),
        ], $tableOptions);
        
        $user = new User();
        $user->username = "administrator";
        $user->fullName = "Administrator";
        $user->password = "administrator";
        $user->email = "admin@admin.com";
        $user->generateAuthKey();
        $user->save();

        $transaction->commit();
        
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
