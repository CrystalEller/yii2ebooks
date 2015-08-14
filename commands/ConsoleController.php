<?php

namespace app\commands;


use app\models\User;
use yii\console\Controller;

class ConsoleController extends Controller
{
    public function actionInitAcl()
    {
        $auth = \Yii::$app->authManager;

        $rbacPath=\Yii::$app->basePath.'/rbac/';

        if(!file_exists($rbacPath)) {
            mkdir($rbacPath, 0755, true);
        }

        $role = $auth->createRole('admin');

        $auth->add($role);

        $password = \Yii::$app->getSecurity()->generatePasswordHash('admin');

        $conn = \Yii::$app->db;

        $conn->createCommand()
            ->insert('user', [
                'login' => 'admin',
                'email' => 'test@test.ru',
                'password' => $password,
                'stat' => User::STATE_ACTIVE,
                'role' => 'admin',
            ])->execute();

        $auth->assign($role, $conn->getLastInsertID());

        echo 'Acl successfully init';
    }
}
