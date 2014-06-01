<?php
require_once 'Zend/Captcha/Image.php';

class Z_Captcha_Image extends Zend_Captcha_Image
{
    /**
     * Текст ссылки для обновления капчи
     *
     * @var string
     */
	protected $_updatelabel = "Не вижу код";
	
	
	 /**
     * Возвращаем текст ссылки обновления капчи
     *
     * @return string
     */
	public function getUpdatelabel()
    {
        return $this->_updatelabel;
    }
    
    /**
     * Задает заголовок для ссылки обновления капчи
     *
     * @param  string $label
     * @return Zend_Captcha_Image
     */
    public function setUpdatelabel($label)
    {
        $this->_updatelabel = $label;
        return $this;
    }
    
	protected $_generatepath = "";
	
	
	 /**
     * Возвращаем путь к скрипту который будет обновлять капчу на сервере
     *
     * @return string
     */
	public function getGeneratepath()
    {
        return $this->_generatepath;
    }
    
    /**
     * Задает путь к скрипту который будет обновлять капчу на сервере
     *
     * @param  string $label
     * @return Zend_Captcha_Image
     */
    public function setGeneratepath($label)
    {
        $this->_generatepath = $label;
        return $this;
    }

 	public function setUseNumbers($_useNumbers)
    {
        $this->_useNumbers = $_useNumbers;
        parent::$VN=parent::$CN=range(0,9);
        return $this;
    }
	public function render(Zend_View_Interface $view = null, $element = null)
    {
    	$script=$updatelink='';
    	if ($this->getGeneratepath())
    	{
    	$script = '<script type="text/javascript">
			$(document).ready (function() {
				$(\'#update\').click (function(){
					$.getJSON (\''.$this->getGeneratepath().'\',{}, function(json) {
						$(\'#imgcap\').attr(\'src\',\'/captcha/\'+json.id+\'.png\');
						$(\'#cap-id\').val(json.id);
					});
				});
			});
			</script>';
    	$updatelink = '<a href="#" id="update"> '.$this->getUpdatelabel().'</a>';
    	}
    	$imgtag= '<img id="imgcap" width="' . $this->getWidth() . '" height="' . $this->getHeight() . '" alt="' . $this->getImgAlt()
             . '" src="' . $this->getImgUrl() . $this->getId() . $this->getSuffix() . '" />';
    	return $script.$updatelink.$imgtag;
    }
}