<?php

namespace app\modules\gymv\controllers;

use yii\web\Controller;
use League\Csv\Exception;
use League\Csv\Reader;
use app\models\Contact;
use app\models\Address;

/**
 * Default controller for the `gymv` module
 */
class ContactController extends \app\controllers\ContactController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex($tab = 'person')
    {
        return $this->render('index');
    }
    public function actionImportCsv()
    {

        $errorMessage = null;
        $records = [];
        try {
            //$csv = Reader::createFromPath('d:\\tmp\\licencies.csv', 'r');
            //$csv = Reader::createFromStream(fopen('d:\\tmp\\licencies-small.csv', 'r'));
            $csv = Reader::createFromStream(fopen('d:\\tmp\\licencies-100.csv', 'r'));
            //$csv = Reader::createFromStream(fopen('d:\\tmp\\licencies.csv', 'r'));
            $csv->setDelimiter(',');
            $csv->setEnclosure('\'');
            $csv->setHeaderOffset(0);
            $csvRecords = $csv->getRecords(['name', 'woman_name', 'firstname','gender', 'birthday', 'license_num',
            'license_cat','residence','locality','street','zip','city','country','phone', 'mobile', 'email']);

            foreach ($csvRecords as $offset => $record) {
                $normalizedRecord = $this->normalizeRecord($record);

                $contact = Contact::find()
                    ->where([
                        'name' => $normalizedRecord['name'],
                        'firstname' => $normalizedRecord['firstname'],
                        'gender' => $normalizedRecord['gender']
                    ])
                    ->one();

                $action = null;
                if ( $contact === null) {
                    $action = 'insert';
                    $contact = new Contact();
                    $contact->setAttributes([
                        'name' => $normalizedRecord['name'],
                        'firstname' => $normalizedRecord['firstname'],
                        'gender' => $normalizedRecord['gender'],
                        'is_natural_person' => true,
                        'birthday' => $normalizedRecord['birthday'],
                        'email' => $normalizedRecord['email'],
                    ]);
                    
                    if (!$contact->save()) {
                        $action .= '-error';
                        $contact = null;
                    } else {
                        // TODO: create related bank account
                        $action .= '-success';
                    }
                }

                if ($contact != null) {
                    // work on address
                    // map CSV columns to Address attributes
                    $address = new Address();
                    $address->setAttributes([
                        'line_1' => $normalizedRecord['street'],
                        'line_2' => $normalizedRecord['residence'],
                        'zip_code' => $normalizedRecord['zip'],
                        'city' => $normalizedRecord['city'],
                        'country' => $normalizedRecord['country']
                    ]);

                    $needSave = true;
                    if ($contact->hasAddress) {
                        // may need update address
                        $action .= 'update_address';

                        $existingAddress = $contact->address;
                        if( 
                            $existingAddress->line_1 !=  $address->line_1       ||
                            $existingAddress->line_2 !=  $address->line_2       ||
                            $existingAddress->zip_code !=  $address->zip_code   ||
                            $existingAddress->city !=  $address->city           ||
                            $existingAddress->country !=  $address->country
                            ) {
                                $needSave = true;
                            } else {
                                $needSave = false;
                            }
                    } 
                    if ($needSave) {
                        // insert address
                        $action .= 'insert_address';
                        if ($address->save()) {
                            $action .= ':ok';
                            $contact->link('address', $address);
                        } else {
                            $action .= ':error';
                        }
                    }
                }

                $records['L' . $offset] = [
                    'data' => $normalizedRecord,
                    'action' => $action
                ];
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
        }

        return $this->render('import-csv', [
            'errorMessage' => $errorMessage,
            'records' => $records
        ]);
    }

    private function normalizeRecord($record)
    {
        unset($record['woman_name']);
        unset($record['license_num']);
        unset($record['license_cat']);
        unset($record['locality']);
        // normlize gender
        $record['gender'] = ($record['gender'] == 'Femme' ? '2' : '1');

        return $record;
    }
}
