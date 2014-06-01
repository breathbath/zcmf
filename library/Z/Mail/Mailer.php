<?php

class Z_Mail_Mailer
{
	protected $options = array();
	private $mailagent = null;

        function __construct($sid,$variables=array(),$options = array())
	{
            $this->set($options);
                $this->mailagent=new Zend_Mail('UTF-8');
            $model = new Z_Model_Mails();
		$mail = $model->fetchRow(array('sid=?'=>$sid));
		if (!$mail) throw new Exception('Для ключа "'.$sid.'" не найден наблон письма.');
		$this->set($mail->toArray());
		
		foreach ($this->get() as $key=>$val)
		{
			$tpl = new Z_View_Template($val,$variables);
			$this->set($key,$tpl->render());
			unset($tpl);
		}
		$this->set($options);
	}
     public function addAttach ($filename){
            if(file_exists($filename))
            {
               
                 $att = file_get_contents($filename);
                 //file_put_contents(APPLICATION_PATH.'/word.doc', $att);
                 $at = new Zend_Mime_Part($att);
                 $at->filename = basename($filename);
                 $at->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
                 $at->encoding = Zend_Mime::ENCODING_BASE64;
                 $this->mailagent->addAttachment($at);
                 //$at = $this->mailagent->addAttachment($att);
                 
            }    
            
        }

	public function send()
	{
		
		//die(var_dump($this->options));
		
		$this->mailagent->setBodyHtml($this->get('message','message'),'UTF-8',Zend_Mime::ENCODING_BASE64);
		$this->mailagent->setFrom($this->get('from','from'),$this->get('fromname','fromname'));
		$this->mailagent->addTo($this->get('to','to'));
		$this->mailagent->setSubject($this->get('theme','theme'));
		$this->mailagent->send();
	}

	public function getBody()
	{
		return $this->get('message','message');
	} 
	
	public function get($key=NULL,$default=NULL)
	{
		if ($key)
		{
			if (isset($this->options[$key]))
				return $this->options[$key];
			else
			{
				if ($default) return $default;
				return NULL;
			}
		}
		else
		{
			return $this->options;
		}
	}
	
	public function set($key,$value=NULL)
	{
		if (is_array($key))
		{
			foreach ($key as $elkey=>$el)
			{
				$this->options[$elkey] = $el;
			}
		}
		else
		{
			if ($value!=NULL)
				$this->options[$key] = $value;
			else
				unset($this->options[$key]);
		}
		return $this;
	}	
	
	
}

?>