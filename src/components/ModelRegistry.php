<?php

namespace app\components;

use Yii;

/**
 * This class is an abstraction layer between data models and their actual implementation.
 * Each data model is identified by a string which gives access to various related attributes. For example
 * the name of the table used to store the model.
 */
class ModelRegistry
{
    const CONTACT = 'contact';
    const ADDRESS = 'address';
    const BANK_ACCOUNT = 'bank_account';
    const ATTACHMENT = 'attachment';
    const CATEGORY = 'category';
    const ORDER = 'order';
    const PRODUCT = 'product';
    const TRANSACTION = 'transaction';
    const TRANSACTION_PACK = 'transaction_pack';
    const CONTACT_RELATION = 'contact_has_contact';

    /**
     * Model registry map
     */
    private static $_map = null;

    /**
     * Initialize the registry map if not already done.
     * The map can't be declared as a class member because it contains function
     * calls.
     *
     * @return void
     */
    public static function init()
    {
        if (self::$_map != null) {
            return;
        }

        self::$_map = [
            self::CONTACT => [
                'tableName' => \app\models\Contact::tableName(),
                'label' => 'contact'
            ],
            self::ADDRESS => [
                'tableName' => \app\models\Address::tableName(),
                'label' => 'address'
            ],
            self::BANK_ACCOUNT => [
                'tableName' => \app\models\BankAccount::tableName(),
                'label' => 'bank account',
                'viewRoute' => 'bank-account/view'
            ],
            self::ATTACHMENT => [
                'tableName' => \app\models\Attachment::tableName(),
                'label' => 'attachment'
            ],
            self::CATEGORY => [
                'tableName' => \app\models\Category::tableName(),
                'label' => 'category'
            ],
            self::ORDER => [
                'tableName' => \app\models\Order::tableName(),
                'label' => 'order'
            ],
            self::PRODUCT => [
                'tableName' => \app\models\Product::tableName(),
                'label' => 'product'
            ],
            self::TRANSACTION => [
                'tableName' => \app\models\Transaction::tableName(),
                'label' => 'transaction'
            ],
            self::TRANSACTION_PACK => [
                'tableName' => \app\models\TransactionPack::tableName(),
                'label' => 'transaction pack',
                'viewRoute' => 'transaction-pack/view'
            ],
            self::CONTACT_RELATION => [
                'tableName' => \app\models\ContactRelation::tableName(),
                'label' => 'contact relation',
                'viewRoute' => 'contact-relation/view'
            ],
        ];
    }

    /**
     * Returns an object describing the data model being given its id.
     * The returned value is a generic object.
     *
     * @param string $id the data model id to retrieve
     * @return object data model descriptor or NULL if not found
     */
    public static function getById($id)
    {
        self::init();

        if (array_key_exists($id, self::$_map)) {
            // warning : array to object conversion fails for nested arrays
            // see https://stackoverflow.com/questions/19272011/how-to-convert-an-array-into-an-object-using-stdclass
            $result = (object) self::$_map[$id];
            $result->id = $id;

            return $result;
        } else {
            return null;
        }
    }
    /**
     * Returns an object describing a data model being given its table name
     *
     * @param string $tableName
     * @return object data model descriptor or NULL if not found
     */
    public static function getByTableName($tableName)
    {
        self::init();

        foreach (self::$_map as $id => $model) {
            if ($model['tableName'] == $tableName) {
                return self::getById($id);
            }
        }
        return null;
    }

    public static function getTableNameIndex()
    {
        self::init();
        $result = [];
        foreach (self::$_map as $id => $model) {
            $result[$model['tableName']] = $model['label'];
        }
        
        return $result;
    }
}
