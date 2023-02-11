<?php

class Accounts extends CI_Controller
{
    
    public function edit($flag = NULL)
    {
        $this->load->model('accounts_model'); //宣告model
        $this->load->library('session'); //宣告session library

        if (!empty($flag)) { //資料更新時

            $userId = $this->session->userdata('userID'); //抓session帳號資料

            if (empty($userId)) { //判斷是否登入
        
                //導向登入頁
                die('<script>location.href="/accounts/login";</script>');

            }
                
            if ($flag != 'set') { //限定進來的字段
                
                die('<script>alert("資料導向錯誤。");location.href="/"</script>');

            }

            //DB抓資料
            $data = $this->accounts_model->getData($userId);

            if (!$data) { //資料庫防呆
                
                die('<script>alert("資料導向錯誤。");location.href="/"</script>');

            }

        } else {
            
            $userId = Null;

        }
        
        if ($this->input->method() != 'post') { //表單未送出時

            if (!empty($flag)) { //資料更新時

                $data['pwdRequired'] = '';
                $data['methodAction'] = '/accounts/edit/set';
                $data['methodReadonly'] = 'readonly disabled';
                $data['methodTitle'] = '更新';

            } else { //資料新增時

                $data['pwdRequired'] = 'required';
                $data['methodAction'] = '/accounts/edit';
                $data['methodReadonly'] = '';
                $data['methodTitle'] = '建立';

            }
            
            $data['userRealName'] = $this->session->userdata('userRealName');
            $data['csrfName'] = $this->security->get_csrf_token_name();
            $data['csrfHash'] = $this->security->get_csrf_hash();

            echo $this->twig->render('account/edit_form.twig',$data);

        } else { //表單送出時

            $inpuDatas = $this->input->post(NULL,TRUE); //抓post進來的資料, 第二參數xss過濾

            $inpuDatas['account_pwd'] = password_hash($inpuDatas['account_pwd'], PASSWORD_DEFAULT); //密碼編碼
           
            $chk = $this->accounts_model->saveData($inpuDatas, $userId); //model 儲存

            if ($chk) {
                
                die('<script>alert("資料儲存成功。");location.href="/"</script>');

            } else {
                
                die('<script>alert("資料儲存失敗。");history.back();</script>');

            }

        }

    }

    public function login()
    {

        $this->load->model('accounts_model');
        $this->load->library('session');
        
        $userId = $this->session->userdata('userID');
        
        if (!empty($userId)) {
            
            //導向
            die('<script>location.href="/accounts/edit/set";</script>');

        }

        if ($this->input->method() != 'post') {

            $data['csrfName'] = $this->security->get_csrf_token_name();
            $data['csrfHash'] = $this->security->get_csrf_hash();
            
            echo $this->twig->render('account/login_form.twig',$data);

        } else {

            $inpuDatas = $this->input->post(NULL,TRUE); //抓post進來的資料, 第二參數xss過濾

            $chk = $this->accounts_model->login($inpuDatas,'account_name');

            if ($chk) {

                die('<script>alert("登入成功。");location.href="/"</script>');

            } else {

                die('<script>alert("登入失敗");location.href="/accounts/login"</script>');

            }

        }
        
    }
    
    public function logout()
    {
        $this->load->library('session');
        $this->session->sess_destroy();
        die('<script>alert("登出成功。");location.href="/"</script>');
    }

}

?>