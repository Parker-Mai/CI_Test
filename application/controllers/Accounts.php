<?php

class Accounts extends CI_Controller
{
    
    public function edit($id = NULL)
    {
        $this->load->model('accounts_model');
        
        if ($this->input->method() != 'post') {

            if (!empty($id)) {
                
                if (!preg_match("/^[1-9][0-9]*$/",$id)) {
                    
                    die('<script>alert("資料導向錯誤。");location.href="/"</script>');

                }

                $data = $this->accounts_model->getData($id);

                if (!$data) {
                    
                    die('<script>alert("資料導向錯誤。");location.href="/"</script>');

                }

                $data['pwdRequired'] = '';
                $data['methodAction'] = '/accounts/edit/'.$id;
                $data['methodReadonly'] = 'readonly disabled';
                $data['methodTitle'] = '更新';

            } else {

                $data['pwdRequired'] = 'required';
                $data['methodAction'] = '/accounts/edit';
                $data['methodReadonly'] = '';
                $data['methodTitle'] = '建立';

            }
            
            $data['csrfName'] = $this->security->get_csrf_token_name();
            $data['csrfHash'] = $this->security->get_csrf_hash();

            echo $this->twig->render('account/edit_form.twig',$data);

        } else {

            $inpuDatas = $this->input->post();

            $inpuDatas['account_pwd'] = password_hash($inpuDatas['account_pwd'], PASSWORD_DEFAULT);

            $chk = $this->accounts_model->saveData($inpuDatas, $id);

            if ($chk) {

                die('<script>alert("資料儲存成功。");location.href="/"</script>');

            } else {

                die('<script>alert("資料儲存失敗。");history.back();</script>');

            }

        }

    }

    public function login()
    {
        echo $this->twig->render('account/login_form.twig');
    }
    
    public function logout()
    {
        
    }

}

?>