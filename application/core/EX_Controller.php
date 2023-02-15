<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class EX_Controller extends CI_Controller {

	public $loader;
	public $twig;

	public function __construct()
	{
		parent::__construct();

		//twig 模板引擎宣告 START
			$this->loader = new \Twig\Loader\FilesystemLoader(VIEWPATH);
			$this->twig = new \Twig\Environment($this->loader);
		//twig 模板引擎宣告 END

		$this->getSystemData();

	}

	/**
	 * 抓system資料
	 */
	private function getSystemData()
	{
		
		$this->load->model('system_model'); //宣告model
        $this->load->library('session');

        $data = $this->system_model->getDataById(1);
		
        //放session
        $this->session->set_userdata([
            'system' => [
				'webTitle' => $data['web_title'],
                'templateType' => $data['frontend_template']
            ]
        ]);

	} 

}
