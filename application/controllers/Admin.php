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
        
        echo $this->twig->render('backend/index.twig', $this->outData);

    }

}

?>