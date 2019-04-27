<?php
namespace app\components\actions;

use Yii;
use yii\base\Action;
use app\models\forms\UserContactForm;
use yii\web\NotFoundHttpException;
use app\components\Constant;
use app\models\Contact;
use app\components\SessionContact;

class UserContactAction extends Action
{
    public function run($redirect_url = null, $clear = 0)
    {
        //shortcuts
        $session = Yii::$app->session;
        $conf = Yii::$app->configManager; 

        // request to clear contact data
        if (Yii::$app->request->isGet && $clear == 1) {
            SessionContact::clear();
            $conf->clearValue('contact_id');
            $conf->clearValue('bank_account_id');

            return $this->controller->redirect($redirect_url);
        }

        // request to set Contact data
        $model = new UserContactForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            SessionContact::setContact($model->contact_id);
            $conf->getItem('contact_id')->setValue(SessionContact::getContactId());
            $conf->getItem('bank_account_id')->setValue(SessionContact::getBankAccountId());
            $conf->saveValues();
            
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
