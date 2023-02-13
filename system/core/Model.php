<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2019 - 2022, CodeIgniter Foundation
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @copyright	Copyright (c) 2019 - 2022, CodeIgniter Foundation (https://codeigniter.com/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/userguide3/libraries/config.html
 */
class CI_Model {

	protected $table; //宣告使用table

	protected $primaryKey = 'id'; //宣告主鍵

	protected $timeAutoUpdate = TRUE; //是否自動更新時間

	protected $softDeletes = FALSE; //是否假刪

	protected $loginField = ''; //宣告如果登入驗證的帳號欄位名稱

	protected $passWordHash = ''; //宣告哪一欄位要編碼

	protected $dateFormat = 'Y/m/d H:i:s'; //時間格式

	protected $getProcessRule = []; //資料取出時，對欄位做資料處理規則

	/**
	 * Class constructor
	 *
	 * @link	https://github.com/bcit-ci/CodeIgniter/issues/5332
	 * @return	void
	 */
	public function __construct() {

		$this->load->database();

	}

	/**
	 * __get magic
	 *
	 * Allows models to access CI's loaded classes using the same
	 * syntax as controllers.
	 *
	 * @param	string	$key
	 */
	public function __get($key)
	{
		// Debugging note:
		//	If you're here because you're getting an error message
		//	saying 'Undefined Property: system/core/Model.php', it's
		//	most likely a typo in your model code.
		return get_instance()->$key;
	}

	public function getData($id = NULL, $where = [])
	{
		if ($id === NULL) { //多筆資料

			if ($this->softDeletes) { //假刪觸發，排除假刪除資料
				$where['deleted_at ='] = '';
			}

			if (count($where) > 0) { //where子句帶入
				$this->db->where($where);
			}

			$query = $this->db->get($this->table); //查詢

			$result = $this->getProcess($query->result_array(),'multi');

			return $result;
            
        }

        $query = $this->db->get_where($this->table, array($this->primaryKey => $id)); //單筆查詢

		if(empty($query->row_array())){
			
			return FALSE;
		}

		$result = $this->getProcess($query->row_array(),'single');

        return $result;
	}

	public function saveData($datas = [], $id = NULL)
	{
		
		if (!empty($this->passWordHash) && array_key_exists($this->passWordHash, $datas)) { //密碼編碼
			
			$datas[$this->passWordHash] = password_hash($datas[$this->passWordHash], PASSWORD_DEFAULT);

		}
		
		if ($id === NULL) {
			
			if ($this->timeAutoUpdate) {

				$datas['create_at'] = date($this->dateFormat);
				$datas['update_at'] = date($this->dateFormat);

			}

			return $this->db->insert($this->table, $datas);

		} else {

			if ($this->timeAutoUpdate) {

				$datas['update_at'] = date($this->dateFormat);

			}

			$this->db->where($this->primaryKey, $id);
			return $this->db->update($this->table, $datas);

		}

	}

	public function deleteData($id)
	{
		
		if ($this->softDeletes) {
			
			$datas['deleted_at'] = date($this->dateFormat);

			$this->db->where($this->primaryKey, $id);
			return $this->db->update($this->table, $datas);

		} else {

			$this->db->where($this->primaryKey, $id);
			return $this->db->delete($this->tabl);

		}

	}

	public function getProcess($data, $type)
	{

		if ($type == 'multi') {
			
			foreach ($this->getProcessRule as $field => $rule) {
				
				foreach ($data as $k => $v) {
					
					if (array_key_exists($field, $v)) {
						
						foreach ($rule as $dbData => $outData) {
							
							if ($v[$field] == $dbData) {

								$data[$k][$field] = $outData;

							}
							

						}

					} 

				}

			}

		} elseif ($type == 'single') {
			
			foreach ($this->getProcessRule as $field => $rule) {

				if (array_key_exists($field, $data)) {
						
					foreach ($rule as $dbData => $outData) {
						
						if ($data[$field] == $dbData) {

							$data[$field] = $outData;

						}
						

					}

				} 

			}

		}

		return $data;

	}

	private function studly($string)
	{
		
		$stringArr = explode('_',$string);
		
		$stringArr = array_map(function($str){
			return ucfirst($str);
		},$stringArr);

		return implode($stringArr);

	}

}
