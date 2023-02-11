<?php

class Accounts extends CI_Controller
{

    public function index()
    {
        $this->load->model('accounts_model'); //宣告model

        $data['dataList'] = $this->accounts_model->getData();

        echo $this->twig->render('backend/accounts/list.twig',$data);
    }

    public function edit($id = NULL)
    {
        $this->load->model('accounts_model'); //宣告model

        if ($this->input->method() != 'post') { //表單未送出時

            if ($id !== NULL) { //資料更新時

                $data = $this->accounts_model->getData($id);
    
                $data['pwdRequired'] = '';
                $data['methodAction'] = '/admin/accounts/edit/'.$id;
                $data['methodReadonly'] = 'disabled';
                $data['methodTitle'] = '編輯';

            } else {
                
                $data['pwdRequired'] = 'required';
                $data['methodAction'] = '/admin/accounts/edit';
                $data['methodReadonly'] = '';
                $data['methodTitle'] = '新增';
    
            }

            $data['csrfName'] = $this->security->get_csrf_token_name();
            $data['csrfHash'] = $this->security->get_csrf_hash();

            echo $this->twig->render('backend/accounts/edit_form.twig',$data);

        } else { //表單送出時

            $inputDatas = $this->input->post(NULL,TRUE); //抓post進來的資料, 第二參數xss過濾

            if ($id !== NULL) { //資料更新時
                
                if (empty($inputDatas['account_pwd'])) { //如果密碼沒填

                    unset($inputDatas['account_pwd']); //陣列把密碼欄位拿掉(不更新密碼)

                }

            }
           
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

}

?>