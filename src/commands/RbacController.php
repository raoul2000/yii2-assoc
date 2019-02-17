<?php
namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        $manageUser = $auth->createPermission('manageUser');
        $manageUser->description = 'Gestion des utilisateurs du site';
        $auth->add($manageUser);
        
        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $manageUser);
/*
        // ajoute un rôle "admin" role et donne à ce rôle la permission "updatePost"
        // aussi bien que les permissions du rôle "author"
        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $updatePost);
        $auth->addChild($admin, $author);

        // Assigne des rôles aux utilisateurs. 1 et 2 sont des identifiants retournés par IdentityInterface::getId()
        // ordinairement mis en œuvre dans votre modèle  User .
        $auth->assign($author, 2);
        $auth->assign($admin, 1);
*/        
    }
}