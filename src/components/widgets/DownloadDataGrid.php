<?php

namespace app\components\widgets;

use Yii;
use yii\web\View;
use yii\base\Widget;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\Constant;
use app\components\SessionContact;

class DownloadDataGrid extends Widget
{
    const TRIGGER_HEADER_NAME = 'X-Download-Report';
    public $url;
    public $defaultFilename = 'file.csv'; 
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
        $headerName = self::TRIGGER_HEADER_NAME;
        $jsScript=<<<EOS
        const downloadReport = (ev) => {
            ev.target.disabled = true;
            $.ajax({
                url: '$this->url',
                method: 'GET',
                headers : {
                    "$headerName" : true
                },
                xhrFields: {
                    responseType: 'blob'
                }
            })
            .done((data, textStatus, jqXHR) => {
                const responseContentType = jqXHR.getResponseHeader("content-type") || "";
                if( responseContentType.indexOf('text/csv') == -1) {
                    alert('Unexpected data format received : "csv" is expected');
                    return;
                }
                try {
                    // Try to find out the filename from the content disposition `filename` value
                  const disposition = jqXHR.getResponseHeader('content-disposition');
                  const matches = /"([^"]*)"/.exec(disposition);
                  const filename = (matches != null && matches[1] ? matches[1] : '$this->defaultFilename');
  
                  const a = document.createElement('a');
                  const url = window.URL.createObjectURL(data);
                  a.href = url;
                  a.download = filename;
                  document.body.append(a);
                  a.click();
                  a.remove();
                  window.URL.revokeObjectURL(url);
                } catch(err) {
                    alert('something went really wrong : failed to download data');
                    console.error(err);
                }
            })
            .fail((err) => {
                alert('Failed to download');
                console.error(err);
            })
            .always( () => {
                console.log('always');
                ev.target.disabled = false;
            });                
        };
        document.getElementById('btn-export-report').addEventListener('click', downloadReport);
EOS;
    
        $this->getView()->registerJs($jsScript, View::POS_READY, 'add-product-to-cart');        
    }
    public function createButton()
    {
        return Html::button(
            '<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> '
                . $this->label, 
            [
                'id' => 'btn-export-report', 
                'title' => 'Download data',
                'class' => 'btn btn-default',  
                'data-pjax'=>0]
        );
    }

    static public function isDownloadRequest()
    {
        $headers = Yii::$app->request->getHeaders();
        return $headers->has(self::TRIGGER_HEADER_NAME);
    }
    static public function downloadAction()
    {

    }
}
