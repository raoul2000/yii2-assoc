<?php

namespace app\controllers\admin;

use Yii;
use Da\User\Model\User as UserModel;

class InstallController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $success = true;
        $message ='';

        $adminUser = UserModel::findOne(['username' => 'admin']);
        if ($adminUser == null) {
            $adminUser = $this->createAdminUser();
        }
        
        if ($adminUser->hasErrors()) {
            $success = false;
            $message = 'Error';
        } else {
            $authManager = Yii::$app->authManager;

            $adminRole = $this->createAdminRole();
            if (!$authManager->checkAccess($adminUser->getID(), 'admin')) {
                $authManager->assign($adminRole, $adminUser->getID());
            }

            $message = "Admin user created and appropriate rolae assigned";
        }

        return $this->render('index', [
            'success' => $success,
            'message' => $message
        ]);
    }

    private function createAdminUser()
    {
        $model = new UserModel();
        $model->confirmed_at = time();
        $model->password = '123456';
        $model->save();

        return $model;
    }
    /**
     * Create the 'admin' role is not already exist
     *
     * @return void
     */
    private function createAdminRole()
    {
        $auth = Yii::$app->authManager;
        $adminRole = $auth->getRole('admin');
        if (!$adminRole) {
            $admiRole = $auth->createRole('admin');
            $auth->add($adminRole);
        }
        return $adminRole;
    }
}
