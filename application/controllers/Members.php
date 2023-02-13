<?php

class Members extends CI_Controller
{
    private $validationRules = [
        'member_name' => [
            'label' => '帳號',
            'rules' => 'required',
            'errors' => [
                'required' => '{field} 為必填',
                'is_unique' => '{field}已存在，請使用其他帳號'
            ]
        ],
        'member_pwd' => [
            'label' => '密碼',
            'rules' => 'required',
            'errors' => [
                'required' => '{field} 為必填'
            ]
        ],
        'member_realname' => [
            'label' => '姓名',
            'rules' => 'required',
            'errors' => [
                'required' => '{field} 為必填'
            ]
        ],
        'member_email' => [
            'label' => '電子信箱',
            'rules' => 'valid_emails',
            'errors' => [
                'valid_emails' => '請輸入正確的{field}格式'
            ]
        ],
    ];

    public function edit($flag = NULL)
    {
        $this->load->model('members_model'); //宣告model
        $this->load->library('session'); //宣告session library
        $this->load->library('auth');
        
        if (!empty($flag)) { //資料更新時

            if ($flag != 'set') { //限定進來的字段
                
                die('<script>alert("資料導向錯誤。");location.href="/"</script>');

            }

            if (!$this->auth->loginCheck('web')) { //判斷是否登入
        
                //導向登入頁
                die('<script>location.href="/main/login";</script>');

            } else {

                $userData = $this->auth->userData;

            }

            //DB重新抓資料
            $data = $this->members_model->getData($userData['frontendUser']['ID']);

            if (!$data) { //資料庫防呆
                
                die('<script>alert("資料導向錯誤。");location.href="/"</script>');

            }

        }
        
        if ($this->input->method() != 'post') { //表單未送出時

            if (!empty($flag)) { //資料更新時

                $data['pwdRequired'] = '';
                $data['methodAction'] = '/main/members/edit/set';
                $data['methodReadonly'] = 'readonly disabled';
                $data['methodTitle'] = '更新';

            } else { //資料新增時

                $data['pwdRequired'] = 'required';
                $data['methodAction'] = '/main/members/edit';
                $data['methodReadonly'] = '';
                $data['methodTitle'] = '建立';
                
            }

            $data['userRealName'] = isset($userData['frontendUser']['member_realname']) ? $userData['frontendUser']['member_realname'] : "";
            $data['csrfName'] = $this->security->get_csrf_token_name();
            $data['csrfHash'] = $this->security->get_csrf_hash();

            echo $this->twig->render('frontend/members/edit_form.twig',$data);

        } else { //表單送出時

            $inputDatas = $this->input->post(NULL,TRUE); //抓post進來的資料, 第二參數xss過濾

            if (!empty($flag)) { //資料更新時
                
                if (empty($inputDatas['member_pwd'])) {

                    unset($inputDatas['member_pwd']);
                    unset($this->validationRules['member_pwd']);
                }

            } else {

                $this->validationRules['member_name']['rules'] .= "|is_unique[ci_members.member_name]"; //帳號欄位驗證加上存在判斷

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
           
            $chk = $this->members_model->saveData($inputDatas, isset($userData['frontendUser']['ID']) ? $userData['frontendUser']['ID'] : ""); //model 儲存

            if ($chk) {
                
                die('<script>alert("資料儲存成功。");location.href="/"</script>');

            } else {
                
                die('<script>alert("資料儲存失敗。");history.back();</script>');

            }

        }

    }
    
}

?>