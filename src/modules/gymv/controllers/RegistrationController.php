<?php
namespace app\modules\gymv\controllers;

use Yii;
use app\models\Address;
use app\models\Contact;
use app\models\AddressSearch;
use yii\web\NotFoundHttpException;

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
    public function actionContactSearch()
    {
        $contact = new Contact();
        $contact->is_natural_person = true;

        if (Yii::$app->request->getIsPost()) {
            $contactId = Yii::$app->request->post('contactId', null);
            if (empty($contactId)) {
                // contact was not found in DB : create a new one
                $contact->name = '';
            } elseif( is_numeric($contactId) ) {
                // existing contact selected (id provided) : validate it exists
                $contact = Contact::find()
                    ->where([
                        'id'                => $contactId,
                        'is_natural_person' => true
                    ])
                    ->one();
                if ($contact == null) {
                    throw new NotFoundHttpException('Contact not found.');
                }    
            } elseif (preg_match('/new-contact@(.+)/', $contactId, $matches, PREG_OFFSET_CAPTURE, 0)) {
                // user entered a contact name that could not be found : create a new contact
                // with entered name (ex : new-contact@Dupond converted to name = 'Dupond')
                $contact->name = $matches[1][0];
            } else {
                throw new NotFoundHttpException('invalid input');
            }
            Yii::$app->session['registration'] = [
                'contact' => $contact->getAttributes()
            ];
            $this->redirect(['contact-edit']);
        }

        return $this->renderWizard(
            $this->renderPartial('_contact-search')
        );
    }

    public function actionContactEdit()
    {
        if (!Yii::$app->session->has('registration') || !array_key_exists('contact', Yii::$app->session['registration'])) {
            // session variable is not as expected
            $this->redirect(['contact-search']);
        }

        $model = Contact::create();
        $model->setAttributes(Yii::$app->session['registration']['contact']);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            Yii::$app->session['registration'] = [
                'contact' => $model->getAttributes()
            ];

            if ( empty($model->address_id)) {
                return $this->redirect(['address-search-fr']);
            }
        }

        return $this->renderWizard(
            $this->renderPartial('_contact-edit', [
                'model' => $model
            ])
        );
    }

    public function actionAddressSearchFr()
    {
        if (!Yii::$app->session->has('registration') || !array_key_exists('contact', Yii::$app->session['registration'])) {
            // session variable is not as expected
            $this->redirect(['contact-search']);
        }

        $model = Contact::create();
        $model->setAttributes(Yii::$app->session['registration']['contact']);


        return $this->renderWizard(
            $this->renderPartial('_address-search-fr', [
                'model' => $model
            ])
        );
    }

    public function actionAddressCreate()
    {
        if (!Yii::$app->session->has('registration') || !array_key_exists('contact', Yii::$app->session['registration'])) {
            // session variable is not as expected
            $this->redirect(['contact-search']);
        }

        $model = Contact::create();
        $model->setAttributes(Yii::$app->session['registration']['contact']);


        return $this->renderWizard(
            $this->renderPartial('_address-create', [
                'model' => $model
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
