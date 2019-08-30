<?php


use yii\base\Component;
use yii\httpclient\Client;

class AddressSearchFrRestAPI extends Component
{
    public $baseUrl = 'https://api-adresse.data.gouv.fr/search/';

    private $_httpClient;

    public function getHttpClient()
    {
        if (!is_object($this->_httpClient)) {
            $this->_httpClient = Yii::createObject([
                'class' => Client::className(),
                'baseUrl' => $this->baseUrl,
            ]);
        }
        return $this->_httpClient;
    }

    public function search($q)
    {
        $response = $this->getHttpClient()->get('q', $q)->send();
        if (!$response->isOk) {
            throw new \Exception('Unable search address');
        }
        return $response->data;
    }
}
