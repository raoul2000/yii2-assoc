<?php
namespace app\components\actions;

use Yii;
use yii\base\Action;
use app\models\forms\UserContactForm;
use yii\web\NotFoundHttpException;
use app\components\Constant;
use app\models\Contact;
use app\components\SessionVars;

class UserContactAction extends Action
{
    public function run($redirect_url = null, $clear = 0)
    {
        $session = Yii::$app->session;

        // request to clear contact data
        if (Yii::$app->request->isGet && $clear == 1) {
            SessionVars::clearContact();
            SessionVars::clearBankAccount();
            return $this->controller->redirect($redirect_url);
        }

        // request to set Contact data
        $model = new UserContactForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model = $this->findContactModel($model->contact_id);
            SessionVars::setContact($model->id, $model->name);

            Yii::$app->configManager->getItem('contact_id')->setValue($model->id);
            
            $banAccounts = $model->bankAccounts;
            if (count($banAccounts) != 0) {
                SessionVars::setBankAccount($banAccounts[0]->id, $banAccounts[0]->name);
                Yii::$app->configManager->getItem('bank_account_id')->setValue($banAccounts[0]->id);
            }

            Yii::$app->configManager->saveValues();
            return $this->controller->redirect($redirect_url);
        }

        return $this->controller->render('/common/user-contact', [
            'model' => $model,
            'contactNames' => \app\models\Contact::getNameIndex(),
            'redirect_url' => $redirect_url
        ]);
    }

    protected function findContactModel($id)
    {
        if (($model = Contact::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested contact does not exist.');
    }
}
