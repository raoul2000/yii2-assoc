<?php
namespace app\modules\gymv\controllers;

use Yii;
use app\models\Address;
use app\models\Contact;
use app\models\AddressSearch;

class RegistrationController extends \yii\web\Controller
{
    private $_step = ['contact', 'address', 'order', 'transaction'];
    private $_currentStep = 'contact';

    private function renderWizard($mainContent)
    {
        return $this->render('index', [
            'wizMain'    => $mainContent,
            'wizSummary' => $this->buildWizSummary(),
        ]);

    }
    private function buildWizSummary()
    {
        return $this->renderPartial('_summary', [
            'value' => 'value'
        ]);
    }

    public function actionContact()
    {
        $model = Contact::create();

        // only create person contact during registration
        $model->is_natural_person = true;

        if ( isset($model->id)) {
            // check it exists
        } else  {
            
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                // by default, create an account for new contact
                $bankAccount = new BankAccount();
                $bankAccount->contact_id = $model->id;
                $bankAccount->name = '';
                $bankAccount->save(false);

                // go to next step
                return $this->redirect(['address']);
            }
        }

        return $this->renderWizard(
            $this->renderPartial('_contact', [
                'data' => 'data'
            ])
        );
    }
    public function actionAddress($contact_id = null, $redirect_url = null)
    {
        $model = new Address();
        $contact = null;

        if (isset($contact_id)) {
            $contact = Contact::findOne($contact_id);
            if ($contact == null) {
                throw new NotFoundHttpException('Contact not found.');
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (isset($contact)) {
                $contact->updateAttributes([
                    'address_id' => $model->id
                ]);
            }
            if ($redirect_url === null) {
                $redirect_url = ['view', 'id' => $model->id];
            }
            return $this->redirect($redirect_url);
        }

        return $this->render('address', [
            'model' => $model,
            'contact' => $contact,
            'redirect_url' => ($redirect_url ? $redirect_url : ['index'])
        ]);
    }


    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionOrders()
    {
        return $this->render('orders');
    }

}
