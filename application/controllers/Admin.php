<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller
{

    protected $outData;

    //重新宣告建構子 檢查是否登入
    public function __construct()
    {

        parent::__construct();
        
        $this->load->library('auth');

        if (!$this->auth->loginCheck('admin')) {
            
            //導向
            die('<script>location.href="/admin/login";</script>');

        } else {
            
            $this->outData = $this->auth->userData;

        }

    }

    public function index()
    {
     
        $this->load->model('system_model'); //宣告model

        $data = $this->system_model->getData(1);
        
        if ($data['frontend_template'] == 1) {
            
            $this->outData['currentTemplateText01'] = '(目前版型)';

        } else {

            $this->outData['currentTemplateText02'] = '(目前版型)';

        }

        $this->outData['webTitle'] = $data['web_title'];

        $this->outData['csrfName'] = $this->security->get_csrf_token_name();
        $this->outData['csrfHash'] = $this->security->get_csrf_hash();
        
        echo $this->twig->render('backend/index.twig', $this->outData);

    }

    public function templateChange()
    {
        
        if (!$this->input->is_ajax_request()) {
            
            die(json_encode(['status' => 'NO', 'message' => '錯誤的導向，版型切換失敗。'], JSON_UNESCAPED_UNICODE));

        }

        $this->load->model('system_model'); //宣告model

        $data = $this->system_model->getData(1);

        if ($data['frontend_template'] == 1) {

            $saveData['frontend_template'] = 2;

        } else {

            $saveData['frontend_template'] = 1;

        }

        $chk = $this->system_model->saveData($saveData,1); //model 儲存

        if (!$chk) {
            
            die(json_encode(['status' => 'NO', 'message' => '資料儲存異常，版型切換失敗。'], JSON_UNESCAPED_UNICODE));

        } else {
            
            die(json_encode(['status' => 'YES', 'message' => '版型切換成功。', 'data' => $saveData], JSON_UNESCAPED_UNICODE));

        }



    }

    public function saveSetting()
    {
        
        if (!$this->input->is_ajax_request()) {
            
            die(json_encode(['status' => 'NO', 'message' => '錯誤的導向，資料儲存失敗。'], JSON_UNESCAPED_UNICODE));

        }


        $this->load->model('system_model'); //宣告model

        $inputDatas = $this->input->post(NULL,TRUE);

        $chk = $this->system_model->saveData($inputDatas,1); //model 儲存

        if (!$chk) {
            
            die(json_encode(['status' => 'NO', 'message' => '資料儲存異常。'], JSON_UNESCAPED_UNICODE));

        } else {
            
            die(json_encode(['status' => 'YES', 'message' => '資料儲存成功。', 'data' => $inputDatas], JSON_UNESCAPED_UNICODE));

        }

    }

}

?>