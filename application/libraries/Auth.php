<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Auth
{
    
    protected $CI;

    private $authSetting;

    public $userData = [];

    public function __construct()
    {
        $this->CI =& get_instance(); //宣告CI框架的所有實例

        $this->authSetting = $this->CI->config->config['auth']; //抓登入驗證設定值
    }

    //登入驗證
    public function attempt($gate='admin', $loginName='', $loginPwd='', $isDisabled = FALSE)
    {
        
        if (empty($loginName) || empty($loginPwd)) {

            return FALSE;

        }

        $authSetting = $this->authSetting[$gate]; //抓config 驗證設定

        $modelName = $authSetting['model'];

        $this->CI->load->model($modelName);
        
        $model = $this->CI->$modelName;

        //開始驗證
        $dbData = $model->getDataByField($loginName, '', TRUE, $isDisabled);
        
        if(!$dbData){

			return FALSE;

		} else {
           
            //密碼驗證
            if (!$model->pwdVerify($loginPwd, $dbData)) {
        
                return FALSE;

            }

            //欄位過濾
            $dbData = $model->hiddenProcess($dbData,'single');

            if ($gate == 'admin') {

                $saveField = 'backendUser';

            } elseif ($gate == 'web') {
            
                $saveField = 'frontendUser';

            } 

            switch ($authSetting['storage']) {
                
                case 'session': //資料放session
                    
                    $this->CI->load->library('session');

                    $this->CI->session->set_userdata([
                        $saveField => $dbData
                    ]);

                    break;

            }

            //更新登入IP、登入時間
			$model->saveData([
                'last_login_ip' => $this->CI->input->ip_address(),
                'last_login_at' => date('Y/m/d H:i:s')
            ], $dbData['ID']);

            $this->userData = $dbData;

            return TRUE;

        }

    }

    //檢查登入
    public function loginCheck($gate, $isDisabled = FALSE)
    {

        if ($gate == 'admin') {

            $saveField = 'backendUser';

        } elseif ($gate == 'web') {
        
            $saveField = 'frontendUser';

        } 

        $authSetting = $this->authSetting[$gate]; //抓config 驗證設定
        
        switch ($authSetting['storage']) {
            
            case 'session':

                $this->CI->load->library('session');
                $userData = $this->CI->session->userdata($saveField);
                break;

        }

        if (empty($userData['ID'])) {
            
            return FALSE;

        } else {
            
           return $this->reFreshData($gate, $userData, $isDisabled);

        }

    }

    //刷新使用者資料
    public function reFreshData($gate, $userData, $isDisabled = FALSE)
    {

        $authSetting = $this->authSetting[$gate]; //抓config 驗證設定

        $modelName = $authSetting['model'];

        $this->CI->load->model($modelName);
        
        $model = $this->CI->$modelName;

        $dbData = $model->getDataByField($userData, '', TRUE, $isDisabled);
        
        if ($gate == 'admin') {

            $saveField = 'backendUser';

        } elseif ($gate == 'web') {
        
            $saveField = 'frontendUser';

        } 

        $this->CI->load->library('session');

        if(!$dbData){

            //找不到的話 清掉session
            $this->CI->session->unset_userdata($saveField);
			return FALSE;

		} else {
            
            //欄位過濾
            $dbData = $model->hiddenProcess($dbData,'single');

            switch ($authSetting['storage']) {
                
                case 'session': //資料放session

                    $this->CI->session->set_userdata([
                        $saveField => $dbData
                    ]);

                    break;

            }

            $this->userData[$saveField] = $dbData;

            return TRUE;

        }

    }
    
}

?>