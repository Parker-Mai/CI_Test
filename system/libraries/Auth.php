<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class CI_Auth
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
    public function attempt($gate='admin', $loginName='', $loginPwd='')
    {
        
        if (empty($loginName) || empty($loginPwd)) {

            return FALSE;

        }

        $authSetting = $this->authSetting[$gate]; //抓config 驗證設定

        $modelName = $authSetting['model'];

        $this->CI->load->model($modelName);
        
        $model = $this->CI->$modelName;

        //開始驗證
        $query = $this->CI->db->get_where($model->table, array($model->loginField => $loginName));

        if($query->num_rows() != 1){

			return FALSE;

		} else {

            $dbData = $query->row_array();

            //密碼驗證
            if (!password_verify($loginPwd, $dbData[$model->passWordHash])) {
                
                return FALSE;

            }

            //過濾隱藏欄位
            foreach ($model->hiddenField as $field) {
                
                if (array_key_exists($field, $dbData)) {
                    unset($dbData[$field]);
                }

            } 
            
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
            $datas['last_login_ip'] = $this->CI->input->ip_address();
            $datas['last_login_at'] = date('Y/m/d H:i:s');

			$this->CI->db->where($model->primaryKey, $dbData['ID']);
			$this->CI->db->update($model->table, $datas);

            $this->userData = $dbData;

            return TRUE;

        }

    }

    //檢查登入
    public function loginCheck($gate)
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
            
            $this->reFreshData($gate, $userData);

            return TRUE;
        }

    }

    //刷新使用者資料
    public function reFreshData($gate, $userData)
    {

        $authSetting = $this->authSetting[$gate]; //抓config 驗證設定

        $modelName = $authSetting['model'];

        $this->CI->load->model($modelName);
        
        $model = $this->CI->$modelName;

        $query = $this->CI->db->get_where($model->table, array($model->loginField => $userData[$model->loginField]));

        if($query->num_rows() != 1){

			return FALSE;

		} else {
            
            $dbData = $query->row_array();

            //過濾隱藏欄位
            foreach ($model->hiddenField as $field) {
                
                if (array_key_exists($field, $dbData)) {
                    unset($dbData[$field]);
                }

            } 
            
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

            $this->userData[$saveField] = $dbData;

            return TRUE;

        }

    }
    
}

?>