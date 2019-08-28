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
        $this->registerJs();
        //$this->registerJs2();
        return $this->createButton();
    }

    public function registerJs2()
    {
        $headerName = self::TRIGGER_HEADER_NAME;
        $js=<<<EOS
        document.getElementById('btn-export-report').addEventListener('click', function() {
            
            // from https://dev.to/bnevilleoneill/programmatic-file-downloads-in-the-browser-2cbh

            function downloadBlob(blob, filename) {
                // Create an object URL for the blob object
                const url = URL.createObjectURL(blob);
              
                // Create a new anchor element
                const a = document.createElement('a');
              
                // Set the href and download attributes for the anchor element
                // You can optionally set other attributes like `title`, etc
                // Especially, if the anchor element will be attached to the DOM
                a.href = url;
                a.download = filename || 'download';
              
                // Click handler that releases the object URL after the element has been clicked
                // This is required for one-off downloads of the blob content
                const clickHandler = () => {
                  setTimeout(() => {
                    URL.revokeObjectURL(url);
                    this.removeEventListener('click', clickHandler);
                  }, 150);
                };
              
                // Add the click event listener on the anchor element
                // Comment out this line if you don't want a one-off download of the blob content
                a.addEventListener('click', clickHandler, false);
              
                // Programmatically trigger a click on the anchor element
                // Useful if you want the download to happen automatically
                // Without attaching the anchor element to the DOM
                // Comment out this line if you don't want an automatic download of the blob content
                a.click();
              
                // Return the anchor element
                // Useful if you want a reference to the element
                // in order to attach it to the DOM or use it in some other way
                return a;
              }
              
            const headers = new Headers({
                "$headerName" : true
            });

            const url = window.location.href;
            fetch(url, { 
                method: 'GET',
                headers: headers,
                cache: 'default' 
            })
                .then( response => response.blob())
                .then( blob => { // csv blob
                    debugger;
                    
                    downloadBlob(blob,'file.csv');
                    window.location.replace(url);
                })
                .catch(console.error);
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

            // use the URL of the current page. This is possible because the GridView widget updates
            // the current url when filters are applied to the grid by users (and the "export" button 
            // must exports the filtered data).

            const url = document.location.href;

            $.ajax({
                // Note that the request is made to the current page when the url value is an empty string
                "url" : url,
                "method": 'GET',
                "headers" : {
                    "$headerName" : true
                },
                "xhrFields": {
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
                // mandatory to prevent the back button to trigger downlad when the user wants to 
                // navigate back to the grid view page
                window.location.replace(url);
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
