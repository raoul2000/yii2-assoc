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
        //$this->registerJs();
        $this->registerJs2();
        return $this->createButton();
    }

    public function registerJs2()
    {
        $headerName = self::TRIGGER_HEADER_NAME;
        $js=<<<EOS
        // This will hold the the file as a local object URL
        var _OBJECT_URL;
        
        // Call an AJAX
        document.getElementById('btn-export-report').addEventListener('click', function() {
            var request = new XMLHttpRequest();
            
            request.addEventListener('readystatechange', function(e) {
                if(request.readyState == 2 && request.status == 200) {
                    // Download is being started
                }
                else if(request.readyState == 3) {
                    // Download is under progress
                }
                else if(request.readyState == 4) {
                    // Downloaing has finished
        
                    _OBJECT_URL = URL.createObjectURL(request.response);
        
                    // Set href as a local object URL
                    document.querySelector('#save-file').setAttribute('href', _OBJECT_URL);
                    
                    // Set name of download
                    document.querySelector('#save-file').setAttribute('download', 'img.jpeg');
                    
                    // Recommended : Revoke the object URL after some time to free up resources
                    // There is no way to find out whether user finished downloading
                    setTimeout(function() {
                        window.URL.revokeObjectURL(_OBJECT_URL);
                    }, 60*1000);
                }
            });
            
            request.addEventListener('progress', function(e) {
                var percent_complete = (e.loaded / e.total)*100;
                console.log(percent_complete);
            });
            
            request.responseType = 'blob';
            
            // Downloading a JPEG file
            request.open('get', document.location.href); 
            request.setRequestHeader('$headerName', true);
            
            request.send(); 
        });        
EOS;

        $this->getView()->registerJs($js, View::POS_READY, 'download-data-grid');
    }

    public function registerJs()
    {
        $headerName = self::TRIGGER_HEADER_NAME;
        $jsScript=<<<EOS
        const downloadReport = (ev) => {
            ev.target.disabled = true;
            $.ajax({
                // use the URL of the current page. This is possible because the GridView widget updates
                // the current url when filters are applied to the grid by users (and the "export" button 
                // must exports the filtered data).
                // Note that the request is made to the current page when the url value is an empty string
                url : document.location.href,
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
                ev.target.disabled = false;
            });                
        };
        console.log('loading DownloadDataGrid widget');
        document.getElementById('btn-export-report').addEventListener('click', downloadReport);
EOS;
    
        $this->getView()->registerJs($jsScript, View::POS_READY, 'download-data-grid');        
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
