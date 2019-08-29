<?php

namespace app\components\widgets;

use Yii;
use yii\web\View;
use yii\base\Widget;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use app\components\Constant;
use app\components\SessionContact;

class DownloadDataGrid extends Widget
{
    public $defaultFilename = 'file.csv'; 
    /**
     * Button Label
     *
     * @var string
     */
    public $label;

    public function init()
    {
        parent::init();
        if ($this->label === null) {
            $this->label = \Yii::t('app', 'Export');
        }
    }    
    /**
    * (non-PHPdoc)
    * @see \yii\base\Widget::run()
    */
    public function run()
    {
        $this->registerJs();
        return $this->createButton();
    }

    public function registerJs()
    {
        /**
         * The previous version of this script involved dynmically creating a anchor element to point to
         * the download url and then invoek click() on it. After many tests, it seems that even if the file 
         * was corretly downloaded, it caused a side effect where if the user navigate to another page just 
         * after download and then click on the BACK button, the download is triggered again.
         * 
         * After some tests, it seems the following script is the only way to prevent side effect : it
         * dynamlically set the href attribute after click event continue its bubble up way.
         */
        $jsScript=<<<EOS
        const downloadReport = (ev) => {
            const currentUrl = new URL(window.location);
            const qParams = new URLSearchParams(currentUrl.search);
            qParams.append('download', true);

            const downloadURL = `\${currentUrl.origin}\${currentUrl.pathname}?\${qParams.toString()}`;
            ev.currentTarget.setAttribute('href' , downloadURL );
        };
        console.log('loading DownloadDataGrid widget');
        document.getElementById('btn-export-report').addEventListener('click', downloadReport, true);
EOS;
    
        $this->getView()->registerJs($jsScript, View::POS_READY, 'download-data-grid');        
    }

    public function createButton()
    {
        return Html::a(
            '<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> '
                . $this->label, 
            '#',
            [
                'id'    => 'btn-export-report', 
                'title' => 'Download data',
                'class' => 'btn btn-default',  
                'data-pjax'=>0
            ]
        );
    }

    static public function isDownloadRequest()
    {
        return Yii::$app->request->getQueryParam('download', false) ;
    }
}
