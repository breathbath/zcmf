<?php
class Z_View_Helper_FormMultiimage extends Zend_View_Helper_FormElement {
    /*
     * Опции для конструктора:
     * file_size_limit - максимально загружаемый размер файла в МБ
     * file_upload_limit - количество файлов которые может грузить загрузчик за один раз
     * file_queue_limit - количество файлов, которые будут помещаться в очередь 0 - неограничено
     * 
     * 
     */
    private $_options = null;
    
    private function initSettings(){
        $this->_options = array(
            'file_size_limit'=>$this->getSize(),
            'file_upload_limit'=>100,
            'file_queue_limit'=>10   
       );
        
    }

    
    public function formMultiimage($name = null, $value = null, $attribs = null) {
        $module = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
        $this->initSettings();
        $resid=0;
        if(is_array($attribs)&&count($attribs))
        {
            foreach ($attribs as $key=> $attr)
            {
                if(isset($this->_options[$key]))
                        $this->_options[$key]=$attr;
                if($key=='res')
                    $resid=$attr;
            }
        }   
        //file_size_limit : "'.$this->_options['file_size_limit'].' MB",
    jQuery::evalScript('
   $(document).ready(function(){
    var swfu;    
    swfu = new SWFUpload({
            upload_url: \'/'.$module.'/z_upload/index/resid/'.$attr.'\',
            post_params: {"u_sid" : "'.session_id().'"},
            
            file_size_limit : "1 MB",
            file_types : "*.jpg;*.jpeg;*.png;*.gif",
            file_types_description : "Изображения формата jpg, jpeg, bmp, png, gif",
            file_upload_limit : "'.$this->_options['file_upload_limit'].'",
            file_queue_limit : "'.$this->_options['file_queue_limit'].'",
            swfupload_loaded_handler: swfUploadLoaded,
            file_queue_error_handler : fileQueueError,
            file_queued_handler: fileQueue,
            file_dialog_complete_handler : fileDialogComplete,
            file_dialog_start_handler: fileDialogStart,
            upload_progress_handler : uploadProgress,
            upload_error_handler : uploadError,
            upload_success_handler : uploadSuccess,
            upload_complete_handler : uploadComplete,
            upload_start_handler: uploadStart, 
            flash_color : "#DFEFFC",
            button_placeholder_id : "spanButtonPlaceholder",
            button_width: 100,
            button_height: 23,
            button_text : "<span class=\'butt\'>Загрузить</span>",
            button_text_style : ".butt {color:#2E6E9E; font-weight:bold; text-align:center; font-size: 14px;}",
            button_text_top_padding: 3,
            button_text_left_padding: 0,
            button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
            button_cursor: SWFUpload.CURSOR.HAND,
            button_action: SWFUpload.BUTTON_ACTION.SELECT_FILES,				
            flash_url : "/sys/img/swfupload.swf",
            custom_settings : {
                upload_target : "progress",
                error_div: "upload_error"
            },
          debug: false
        });    
    });

    ');    

   
/*
 *movieName=SWFUpload_0
 *uploadURL=%2Fv220%2Fsimpledemo%2Fupload.php&amp;
 *useQueryString=false&amp;
 *requeueOnError=false&amp;
 *httpSuccess=&amp;
 *assumeSuccessTimeout=0&amp;
 *params=PHPSESSID%3D&amp;
 *filePostName=Filedata&amp;
 *fileTypes=*.*&amp;
 *fileTypesDescription=All%20Files&amp;
 *fileSizeLimit=100%20MB&amp;
 *fileUploadLimit=100&amp;
 *fileQueueLimit=0&amp;
 *debugEnabled=false&amp;
 *buttonImageURL=%2Fv220%2Fsimpledemo%2Fimages%2FTestImageNoText_65x29.png&amp;
 *buttonWidth=65&amp;
 *buttonHeight=29&amp;
 *buttonText=%3Cspan%20class%3D%22theFont%22%3EHello%3C%2Fspan%3E&amp;
 *buttonTextTopPadding=3&amp;
 *buttonTextLeftPadding=12&amp;
 *buttonTextStyle=.theFont%20%7B%20font-size%3A%2016%3B%20%7D&amp;
 *buttonAction=-110&amp;
 *buttonDisabled=false&amp;
 *buttonCursor=-1">
 */
        $xhtml = '<div class="mainblock"> 
                   <div id="select_button" class="select_button">
                     <span id="spanButtonPlaceholder"></span>
                   </div>
                   <a href="#" onclick="clearList(); return false;" class="z-button z-button-top z-ajax ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false">
                    <span class="ui-button-text">Очистить</span>
                   </a>
                   <div class="clear">&nbsp;</div>
                    <div id="progress"></div>
                   <ul id="upload_error"></ul>
                   
                  </div> 
        ';

        return $xhtml;
    }
    
     private function getSize()
        {
            $size1 = ini_get('post_max_size');
            $letter = $size1[strlen($size1)-1];
            $size1 = (integer)$size1;
            switch($letter){
                case 'G': $size1 *=1024;
                case 'M': $size1 *=1024;
                case 'K': $size1 *=1024;
            }
            $size2 = ini_get('upload_max_filesize');
            $letter = $size2[strlen($size2)-1];
            $size2 = (integer)$size2;
            switch($letter){
                case 'G': $size2 *=1024;
                case 'M': $size2 *=1024;
                case 'K': $size2 *=1024;
            }
            if($size1<=0)
                return $size2/1024/1024;
             if($size2<=0)
                return $size1/1024/1024;
             if($size1>$size2)
                return $size2/1024/1024;
             else
                return $size1/1024/1024;

        }

}
