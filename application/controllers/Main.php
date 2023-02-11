<?php

    defined('BASEPATH') OR exit('No direct script access allowed');

    class Main extends CI_Controller
    {

        public function index()
        {
            
            $this->load->library('session');

            $userId = $this->session->userdata('userID');
            
            if (!empty($userId)) { //已登入

                $data = [
                    'userRealName' => $this->session->userData('userRealName'),
                    'action' => 'logout',
                    'actionName' => 'Logout', 
                ];

            } else { //未登入
                
                $data = [
                    'userRealName' => '',
                    'action' => 'login',
                    'actionName' => 'Login', 
                ];

            }

            

            echo $this->twig->render('frontend/index.twig',$data);

        }

    }

?>