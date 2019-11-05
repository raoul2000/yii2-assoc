<?php

namespace app\modules\gymv\models;

use Yii;
use yii\base\Model;
use \app\models\Product;


class ProductSelectionForm extends Model
{
    const GROUP_COURSE = 'courses';

    const ADHESION_VINCENNOIS     = "1";
    const ADHESION_NON_VINCENNOIS = "2";
    /**
     * Defines the type of registration required. Possible values are :
     *  - ADHESION_VINCENNOIS
     *  - ADHESION_NON_VINCENNOIS
     *
     * @var string
     */
    public $adhesion;

    const DEJA_LICENCIE         = "1";
    const ACHAT_LICENCE_ADULTE  = "2";
    const ACHAT_LICENCE_ENFANT  = "3";
    /**
     * Define if the licence should be bought (ACHAT_LICENCE) or if it has 
     * already been bought (DEJA_LICENCIE).
     *
     * @var [type]
     */
    public $achat_licence;

    /**
     * defines if optional insureance is bought or not
     *
     * @var boolean
     */
    public $assurance_extra;
    /**
     * Defines if registration to Sorano is selected or not
     *
     * @var boolean
     */
    public $inscription_sorano;
    /**
     * List of product ids matching selected classes
     *
     * @var array
     */
    public $cours_ids = [];
    /**
     * When TRUE, the future member did provide a ceritifcate, otherwise FALSE
     *
     * @var boolean
     */
    public $justif_certificat;
    /**
     * When TRUE, the future member did provide a attestation, otherwise FALSE
     *
     * @var boolean
     */
    public $justif_attestation;
    /**
     * In case a certificate is provided (justif_certificat = TRUE) enter the validity start date
     *
     * @var string
     */
    public $certif_valid_date_start;
    /**
     * In case a certificate is provided (justif_certificat = TRUE) enter the validity end date
     *
     * @var string
     */
    public $certif_valid_date_end;
    

    public $product_ids = [];
    private $_cat1_product_ids = [];

    public function rules()
    {
        return [
            [['adhesion', 'achat_licence'], 'required'],
            ['adhesion', 'in', 'range' => [
                self::ADHESION_VINCENNOIS,
                self::ADHESION_NON_VINCENNOIS
            ]],
            ['achat_licence', 'in', 'range' => [
                self::DEJA_LICENCIE,
                self::ACHAT_LICENCE_ADULTE,
                self::ACHAT_LICENCE_ENFANT
            ]],
            [['assurance_extra', 'inscription_sorano', 'justif_certificat', 'justif_attestation'], 'boolean'],

            // Validity Date Range ///////////////////////////////////////////////////
            
            [['certif_valid_date_start', 'certif_valid_date_end'], 'date', 'format' => Yii::$app->params['dateValidatorFormat']],
            ['certif_valid_date_start', \app\components\validators\DateRangeValidator::className()],

            // checks if every cours ID is an integer
            ['cours_ids', 'each', 'rule' => ['integer']],     

            [['product_ids'], 'safe'], 
            ['product_ids', 'default', 'value' => []],
        ];
    }    

    /**
     * Returns a list of all courses models selected by the user
     *
     * @return array[Product]
     */
    public function getCoursProductModels() {
        return \app\models\Product::find()
            ->where(['in', 'id', $this->cours_ids] )
            ->indexBy('id')
            ->all();
    }

    /**
     * Returns the product for the select type of "adhésion" 
     *
     * @return Product|NULL
     */
    public function getAdhesionModel() 
    {
        $result = null;
        $productId = null;
        switch ($this->adhesion) {
            case self::ADHESION_VINCENNOIS:
                $productId = Yii::$app->params['registration.product.adhesion_vincennois'];
                break;
                case self::ADHESION_NON_VINCENNOIS:
                $productId = Yii::$app->params['registration.product.adhesion_non_vincennois'];
                break;
        }
        if (!empty($productId)) {
            $result = Product::findOne($productId);
        } 
        return $result;
    }

    /**
     * Returns the product depending on the type of license selected or NULL if
     * no license is selected (Ie. DEJA_LICENCIE)
     *
     * @return Product|NULL
     */
    public function getLicenceModel() 
    {
        $result = null;
        $productId = null;
        switch ($this->achat_licence) {
            case self::ACHAT_LICENCE_ADULTE:
                $productId = Yii::$app->params['registration.product.license_adulte'];
                break;
                case self::ACHAT_LICENCE_ENFANT:
                $productId = Yii::$app->params['registration.product.license_enfant'];
                break;
        }
        if (!empty($productId)) {
            $result = Product::findOne($productId);
        } 
        return $result;
    }
    /**
     * Returns the product model for "Assurance Federation" or NULL if this product 
     * has not been selected
     *
     * @return Product|NULL
     */
    public function getFederationAssuranceModel()
    {
        return empty($this->assurance_extra) || $this->achat_licence === self::DEJA_LICENCIE
            ? null
            : Product::findOne(Yii::$app->params['registration.product.license_assurance']);
    }

    /**
     * Returns the product model for "Inscription Sorano" or NULL if this
     * product has not been selected
     *
     * @return Product|NULL
     */
    public function getSoranoModel()
    {
        return !$this->inscription_sorano
            ? null
            : Product::findOne(Yii::$app->params['registration.product.adhesion_sorano']);
    }
    /**
     * Returns a list of product models depending on the current selected items
     *
     * @return [Products]
     */
    public function getModels()
    {
        $selectedCourseModels = $this->getCoursProductModels();
        $adhesionModel = $this->getAdhesionModel();
        $licenceModel = $this->getLicenceModel();
        $assuranceModel = $this->getFederationAssuranceModel();
        $soranoModel = $this->getSoranoModel();

        // use '+' to concat arrays in order to preserve keys (index)
        return [ $adhesionModel->id => $adhesionModel ] 
            + ( $licenceModel === null ?   [] : [ $licenceModel->id => $licenceModel])
            + ( $assuranceModel === null ? [] : [ $assuranceModel->id => $assuranceModel])
            + ( $soranoModel === null ?    [] : [ $soranoModel->id => $soranoModel])
            + $selectedCourseModels;
    }
    /**
     * Returns the list of product id that have been configured for the given group.
     * 
     * This function is used during the registration wizard, at the product selection step.
     * Depending on group configuration, products are displayed in a different way. 
     * 
     * please check params.php for more.
     *
     * @param string $groupName
     * @return [string]
     */
    static public function getProductIdsByGroup($groupName) 
    {
        $result = [];
        // is there a configuration for product groups ? 
        if( !array_key_exists('registration.product.group', Yii::$app->params)) {
            return $result;
        }
        $groupConf = \Yii::$app->params['registration.product.group'];

        // is there a configuration for this particlar group $groupName ?
        if (!array_key_exists($groupName, $groupConf)) {
            return $result;
        }

        // find what are the product that match this group configuration
        $conf = $groupConf[$groupName]; //shortcut

        $query = Product::find()
            ->select('id')
            ->asArray();

        $productIdKey = $categoryIdKey = false;

        if (array_key_exists('productId', $conf) && is_array($conf['productId'])) {
            $query->where([ 'in', 'id', $conf['productId']]);
            $productIdKey = true;
        }
        if (array_key_exists('categoryId', $conf) && is_array($conf['categoryId'])) {
            $query->orWhere([ 'in', 'category_id', $conf['categoryId']]);
            $categoryIdKey = true;
        }

        // if neither productId nor categoryId condition is configured, return an empty array
        if ( $productIdKey || $categoryIdKey) {
            $rows = $query->all();
            foreach ($rows as $product) {
                $result[] = $product['id']; // assign product Id to result list
            }
        } 
        return $result;
    }    
}
