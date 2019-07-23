<?php

namespace app\components;

use Yii;

class Constant
{
    /**
    * @property boolean when TRUE, the main layout will use a container-fluid CSS class. Otherwise
    * it uses a 'container' CSS class
    */
    const PARAM_FLUID_LAYOUT = 'fluid-layout';
    /**
     * session param name used to store the date range, start and end date.
     */
    const SESS_PARAM_NAME_DATERANGE = 'date_range';
    const SESS_PARAM_NAME_STARTDATE = 'start_date';
    const SESS_PARAM_NAME_ENDDATE = 'end_date';

    /**
     * session param name used to store current Contact info
     */
    const SESS_CONTACT = 'contact';
    /**
     * session param name used to store current Bank Account info
     */
    const SESS_BANK_ACCOUNT = 'bank_account';

    public static function getTransactionTypes()
    {
        return [
            'VIR' => 'VIR - Virement',
            'CHQ' => 'CHQ - Chèque',
            'NUM' => 'NUM - Espèce',
            'CRD' => 'CRD - Carte de Paiement',
        ];
    }

    public static function getTransactionType($type)
    {
        if (empty($type)) {
            return null;
        }
        
        $l = self::getTransactionTypes();
        if (array_key_exists($type, $l)) {
            return $l[$type];
        } else {
            return 'error';
        }
    }

    /**
     * Returns all contact relation types configured.
     * Contact relations can be configured in the file `./config/contact-relations.php`. If not available
     * some default basic types hardcoded in the method, are returned.
     * The expected contact relation structure is :
     *
     * [
     *   'id' => 0,
     *   'name' => 'undefined'
     * ]
     *
     * @return array
     */
    public static function getContactRelationTypes()
    {
        if (array_key_exists('contact-relations', Yii::$app->params)) {
            return Yii::$app->params['contact-relations'];
        } else {
            return [
                [
                    'id' => 1,
                    'name' => 'relation1'
                ],
                [
                    'id' => 2,
                    'name' => 'relation2'
                ],
            ];
        }
    }

    /**
     * Returns a single contact-relation type object given its Id.
     * If no item is found, returns NULL.
     *
     * @param int $typeId
     * @return array
     */
    public static function getContactRelationType($typeId)
    {
        if (empty($typeId)) {
            return null;
        }
        
        $types = self::getContactRelationTypes();
        foreach ($types as $type) {
            if ($type['id'] == $typeId) {
                return $type;
            }
        }
        return null;
    }

    /**
     * Returns the name of a contact-relation-type given its ID, or NULL if not found
     *
     * @param int $typeId
     * @return string
     */
    public static function getContactRelationName($typeId) 
    {
        $type = self::getContactRelationType($typeId);
        return ( empty($type) ? null : $type['name']);
    }
}
