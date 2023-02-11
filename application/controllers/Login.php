<?php

class Login extends CI_Controller
{

    //前台登入
    public function frontendLogin()
    {
        $this->load->model('members_model');
        $this->load->library('auth');

        if ($this->auth->loginCheck('web')) {
            
            //導向
            die('<script>location.href="/main/members/edit/set";</script>');

        }

        if ($this->input->method() != 'post') {

            $data['csrfName'] = $this->security->get_csrf_token_name();
            $data['csrfHash'] = $this->security->get_csrf_hash();
            
            echo $this->twig->render('frontend/login/login_form.twig',$data);

        } else {

            $inputDatas = $this->input->post(NULL,TRUE); //抓post進來的資料, 第二參數xss過濾

            $chk = $this->auth->attempt('web',$inputDatas['login_name'],$inputDatas['login_pwd']);

            if ($chk) {

                die('<script>alert("登入成功。");location.href="/"</script>');

            } else {

                die('<script>alert("帳號或密碼錯誤。");location.href="/main/login"</script>');

            }

        }

    }

    //前台登出
    public function frontendLogout()
    {
        $this->load->library('session');
        $this->session->unset_userdata('frontendUser');
        die('<script>alert("登出成功。");location.href="/"</script>');
    }

    //後台登入
    public function backendLogin()
    {

        $this->load->library('auth');

        if ($this->auth->loginCheck('admin')) {
            
            //導向
            die('<script>location.href="/admin";</script>');

        }

        if ($this->input->method() != 'post') {

            $data['csrfName'] = $this->security->get_csrf_token_name();
            $data['csrfHash'] = $this->security->get_csrf_hash();

            echo $this->twig->render('backend/login/login_form.twig',$data);

        } else {
            
            $inputDatas = $this->input->post(NULL,TRUE); //抓post進來的資料, 第二參數xss過濾

            $chk = $this->auth->attempt('admin',$inputDatas['login_name'],$inputDatas['login_pwd']);

            if ($chk) {

                die('<script>alert("登入成功。");location.href="/admin"</script>');

            } else {

                die('<script>alert("帳號或密碼錯誤。");location.href="/admin/login"</script>');

            }

        }
        
    }

    //後台登出
    public function backendLogout()
    {
        $this->load->library('session');
        $this->session->unset_userdata('backendUser');
        die('<script>alert("登出成功。");location.href="/admin/login"</script>');
    }

}