<?php

class Admin_Z_UploadController extends Zend_Controller_Action
{


	public function indexAction()
	{
            $this->_helper->viewRenderer->setNoRender(true);
            Zend_Layout::getMvcInstance()->disableLayout();
            $data = array('error' => '1', 'errorcode' => '1','preview'=>'', 'path'=>'', 'filename' => '', 'type' => '');
            if(Z_Auth::getInstance()->getUser()->getLogin()=='guest')
            {  
                $this->_forward('index','index');
                return;
            }  

            if (empty($_FILES['Filedata']['name']) || $_FILES['Filedata']['size'] <= 0) {
                $data['errorcode']='2';
                echo Zend_Json::encode($data);
                return;
            }
            $options=array('jpg','jpeg','png','gif');
            $validator = new Z_Validate_File_Extension($options);
            if (!$validator->isValid($_FILES['Filedata']['name'])) {
                $data['errorcode']='3';
                echo Zend_Json::encode($data);
                return;
            }  
        $save_path = SITE_PATH.DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'tmpajdoqODU1';  
        Z_Fs::create_folder($save_path);
        $filename = Z_Transliterator::translateCyr($_FILES['Filedata']['name']);
        $aim = $save_path.DIRECTORY_SEPARATOR.$filename;
        if (!@move_uploaded_file($_FILES['Filedata']['tmp_name'], $aim)) {
                $data['errorcode']='4';
		echo Zend_Json::encode($data);
		return;
	}
   //     $storage = new Z_File_Storage();
        //$debuginfo = $_FILES['Filedata']['tmp_name'] . '---' . $_FILES['Filedata']['name'];
        //$debuginfo = $_FILES['Filedata']['tmp_name'] . '---' . $_FILES['Filedata']['name'];
//        $pf = $storage->create($_FILES['Filedata']['tmp_name'], array(
//          'realname' => $_FILES['Filedata']['name']));
        $prevurl = $this->view->z_Preview($aim, array('w' => 200, 'h' => 170));
        $fileurl = '/upload/tmpajdoqODU1/'.$filename;
        $data = array('error' => '0', 'errorcode'=>'0','preview'=>$prevurl,'path'=>$fileurl, 'filename' => $filename, 'type' => 'pic'); 
        echo Zend_Json::encode($data);

//            foreach($_FILES as $key=>$file)
//		{
//			$new_name = $file['tmp_name'].'_new';
//			move_uploaded_file($file['tmp_name'],$new_name);
//			$_FILES[$key]['tmp_name'] = $new_name;
//		}
//		$nameSpace = new Zend_Session_Namespace('Z-File-Uploader');
//		$nameSpace->files = $_FILES;
	}


}
//        $data = array('error' => '1', 'fileid' => '', 'filename' => '', 'path' => '');
//        if ($this->getRequest()->isXmlHttpRequest()) 
//        {
//         if (empty($_FILES['Filedata']['name']) || $_FILES['Filedata']['size'] <= 0) {
//            $this->view->data = $data;
//        }
//        else
//        {    
//            $options=array('jpg','jpeg','png','gif');
//            $validator = new Z_Validate_File_Extension($options);
//            if (!$validator->isValid($_FILES['Filedata']['name'])) {
//                $this->view->data = $data;
//                return;
//            }
//            $options = array('maxheight' => '1500', 'maxwidth' => '2000');
//            $validator = new Zend_Validate_File_ImageSize($options);
//            if (!$validator->isValid($_FILES['Filedata']['tmp_name'])) {
//                $this->view->data = $data;
//             return;
//            }
//        $storage = new Z_File_Storage();
//        $debuginfo = $_FILES['Filedata']['tmp_name'] . '---' . $_FILES['Filedata']['name'];
//        $pf = $storage->create($_FILES['Filedata']['tmp_name'], array(
//          'realname' => $_FILES['Filedata']['name']));
//        $file = $this->view->z_Preview($pf, array('w' => 160, 'h' => 120));
//        $data = array('error' => '0', 'fileid' => $pf, 'filename' => $_FILES['Filedata']['name'], 'path' => $file, 'type' => 'pic');
//         $this->view->data = $data;


