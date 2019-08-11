<?php

namespace app\controllers\admin;

use Yii;
use Da\User\Model\User as UserModel;

class InstallController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $success = true;
        $username = 'admin';
        $password = '123456';
        $roleName = 'admin';

        $adminUser = $this->createAdminUser($username, $password);
        
        if ($adminUser->hasErrors()) {
            $success = false;
        } else {
            $authManager = Yii::$app->authManager;

            $adminRole = $this->createRole($roleName);
            if (!$authManager->checkAccess($adminUser->getID(), $roleName)) {
                $authManager->assign($adminRole, $adminUser->getID());
            }
        }

        return $this->render('index', [
            'success' => $success,
            'adminUser' => $adminUser,
        ]);
    }

    private function createAdminUser($username, $password)
    {
        $adminUser = UserModel::findOne(['username' => $username]);
        if ($adminUser == null) {
            
            $adminUser = new UserModel();
            $adminUser->username = $username;
            $adminUser->confirmed_at = time();
            $adminUser->password = $password;
            $adminUser->save();
        } 
        return $adminUser;
    }
    /**
     * Create the 'admin' role if not already exist
     *
     * @return void
     */
    private function createRole($roleName)
    {
        $auth = Yii::$app->authManager;
        $adminRole = $auth->getRole($roleName);
        if (!$adminRole) {
            $admiRole = $auth->createRole($roleName);
            $auth->add($adminRole);
        }
        return $adminRole;
    }
}
