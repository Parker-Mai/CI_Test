<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts extends EX_Controller
{

    protected $outData;

    private $validationRules = [
        'account_name' => [
            'label' => '系統帳號',
            'rules' => '',
            'errors' => [
                'required' => '{field} 為必填',
                'is_unique' => '{field}已存在，請使用其他系統帳號'
            ]
        ],
        'account_pwd' => [
            'label' => '系統密碼',
            'rules' => 'required',
            'errors' => [
                'required' => '{field} 為必填'
            ]
        ],
        'account_realname' => [
            'label' => '系統姓名',
            'rules' => 'required',
            'errors' => [
                'required' => '{field} 為必填'
            ]
        ],
        'account_phone' => [
            'label' => '手機',
            'rules' => 'required|valid_phone_tw[3]',
            'errors' => [
                'required' => '{field} 為必填',
                'valid_phone_tw' => '請輸入正確的{field}格式'
            ]
        ],
        'account_email' => [
            'label' => '電子信箱',
            'rules' => 'valid_emails',
            'errors' => [
                'valid_emails' => '請輸入正確的{field}格式'
            ]
        ],
    ];

    //重新宣告建構子 檢查是否登入
    public function __construct()
    {

        parent::__construct();
        
        $this->load->library('auth');

        if (!$this->auth->loginCheck('admin', TRUE)) {
            
            //導向
            die('<script>location.href="/admin";</script>');

        } else {
            
            $this->outData = $this->auth->userData;

        }

        $this->load->model('system_model'); //宣告model

        $data = $this->system_model->getDataById(1);

        $this->outData['webTitle'] = $data['web_title'];

    }

    public function index()
    {
        $this->load->model('accounts_model'); //宣告model

        $this->outData['dataList'] = $this->accounts_model->getDataList();

        $this->outData['csrfName'] = $this->security->get_csrf_token_name();
        $this->outData['csrfHash'] = $this->security->get_csrf_hash();

        echo $this->twig->render('backend/accounts/list.twig',$this->outData);
    }

    public function edit($id = NULL)
    {
        $this->load->model('accounts_model'); //宣告model

        if ($this->input->method() != 'post') { //表單未送出時

            if ($id !== NULL) { //資料更新時

                $this->outData['dbData'] = $this->accounts_model->getDataById($id);
    
                $this->outData['pwdRequired'] = '';
                $this->outData['methodAction'] = '/admin/accounts/edit/'.$id;
                $this->outData['methodReadonly'] = 'disabled';
                $this->outData['methodTitle'] = '編輯';

            } else {
                
                $this->outData['pwdRequired'] = 'required';
                $this->outData['methodAction'] = '/admin/accounts/edit';
                $this->outData['methodReadonly'] = '';
                $this->outData['methodTitle'] = '新增';
    
            }

            $this->outData['csrfName'] = $this->security->get_csrf_token_name();
            $this->outData['csrfHash'] = $this->security->get_csrf_hash();
            
            echo $this->twig->render('backend/accounts/edit_form.twig',$this->outData);

        } else { //表單送出時

            $inputDatas = $this->input->post(NULL,TRUE); //抓post進來的資料, 第二參數xss過濾

            if ($id !== NULL) { //資料更新時
                
                if (empty($inputDatas['account_pwd'])) { //如果密碼沒填

                    unset($inputDatas['account_pwd']); //陣列把密碼欄位拿掉(不更新密碼)
                    unset($this->validationRules['account_pwd']); //驗證陣列密碼拿掉(不檢查密碼)

                }

            } else { //資料新增時
                
                $this->validationRules['account_name']['rules'] .= "required|is_unique[ci_members.member_name]"; //帳號欄位驗證加上必填及存在判斷

            }

            //驗證資料 START
                
                $this->load->library('form_validation');
                $this->form_validation->set_rules_v2($this->validationRules);
                if ($this->form_validation->run() === FALSE) {

                    if (count(validation_errors(TRUE)) > 0) {

                        $error_msg = [];
                        foreach (validation_errors(TRUE) as $field => $msg) {
                            
                            $error_msg[] = $msg."\\n";
    
                        }

                        die("<script>alert('".implode($error_msg)."');history.back();</script>");
                    }

                }
                
            //驗證資料 END
           
            $chk = $this->accounts_model->saveData($inputDatas, $id); //model 儲存

            if ($chk) {
                
                die('<script>alert("資料儲存成功。");location.href="/admin/accounts"</script>');

            } else {
                
                die('<script>alert("資料儲存失敗。");history.back();</script>');

            }

        }

    }

    public function delete($id)
    {
        $this->load->model('accounts_model'); //宣告model

        $chk = $this->accounts_model->deleteData($id);

        if ($chk) {

            die('<script>alert("資料刪除成功。");location.href="/admin/accounts"</script>');

        } else {

            die('<script>alert("資料刪除失敗。");history.back();</script>');
            
        }

    }

    public function disabled()
    {

        if (!$this->input->is_ajax_request()) {
            
            die(json_encode(['status' => 'NO', 'message' => '錯誤的導向，資料儲存失敗。'], JSON_UNESCAPED_UNICODE));

        }

        $this->load->model('accounts_model'); //宣告model

        $inputDatas = $this->input->post(NULL,TRUE);

        $data = $this->accounts_model->getDataById($inputDatas['id']);

        if ($data['is_disabled'] == 1) {

            $saveData['is_disabled'] = 0;

        } else {

            $saveData['is_disabled'] = 1;

        }

        $chk = $this->accounts_model->saveData($saveData,$inputDatas['id']); //model 儲存

        if (!$chk) {
            
            die(json_encode(['status' => 'NO', 'message' => '資料儲存異常，狀態切換失敗。'], JSON_UNESCAPED_UNICODE));

        } else {
            
            die(json_encode(['status' => 'YES', 'message' => '狀態切換成功。', 'data' => $saveData['is_disabled']], JSON_UNESCAPED_UNICODE));

        }

    }

}

?>