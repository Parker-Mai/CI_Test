<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class EX_Model extends CI_Model {

	protected $table; //宣告使用table

	protected $primaryKey = 'id'; //宣告主鍵

	protected $timeAutoUpdate = TRUE; //是否自動更新時間

	protected $softDeletes = FALSE; //是否假刪

	protected $loginField = ''; //宣告如果登入驗證的帳號欄位名稱

	protected $passWordHash = ''; //宣告哪一欄位要編碼

	protected $dateFormat = 'Y/m/d H:i:s'; //時間格式

	protected $getProcessRule = []; //資料取出時，對欄位做資料處理規則

	protected $hiddenField = []; //取出的資料針對欄位隱藏

	public function __construct()
	{
		parent::__construct();
	}

	public function getDataById($id = NULL)
	{

		if ($id === NULL) {
			
			return FALSE;

		}

        $query = $this->db->get_where($this->table, [$this->primaryKey => $id]); //單筆查詢

		if(empty($query->row_array())){
			
			return FALSE;
		}

		$result = $this->getProcess($query->row_array(),'single');
		// $result = $this->hiddenProcess($result,'single');

        return $result;

	}

	public function getDataByField($inputData = NULL, $searchField = '', $auth = FALSE, $isDisabled = FALSE)
	{

		if (!$auth) { //是否驗證
			
			if (empty($searchField)) { //被搜尋欄位為空 回傳false
			
				return FALSE;
	
			} else {
				
				$where = [$searchField => $inputData];

			}

		} else {
			
			if (empty($this->loginField)) {
				
				return FALSE;

			} else {
				
				if (is_array($inputData)) { //如果進來的是陣列，找同名鍵值
					
					$where = [$this->loginField => $inputData[$this->loginField]];
					
				} else {

					$where = [$this->loginField => $inputData];

				}

				//判斷有沒有啟用is_disabled欄位判斷
				if ($isDisabled) {
					
					$where['is_disabled'] = 0;

				}

			}

		}
		
        $query = $this->db->get_where($this->table, $where); //單筆查詢

		if(empty($query->row_array())){
			
			return FALSE;
		}

		$result = $this->getProcess($query->row_array(),'single');
		// $result = $this->hiddenProcess($result,'single');

        return $result;
	}

	public function getDataList($where = [])
	{

		if ($this->softDeletes) { //假刪觸發，排除假刪除資料
			$where['deleted_at ='] = '';
		}

		if (count($where) > 0) { //where子句帶入
			$this->db->where($where);
		}

		$query = $this->db->get($this->table); //查詢

		$result = $this->getProcess($query->result_array(),'multi');
		// $result = $this->hiddenProcess($result,'multi');

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

	public function hiddenProcess($data, $type)
	{

		if ($type == 'multi') {

			//多筆
			foreach ($data as $k => $v) {

				foreach ($this->hiddenField as $field) {

					if (array_key_exists($field, $v)) {

						unset($data[$k][$field]);

					}

				}

			} 

		} elseif ($type == 'single') {
			//單筆
			foreach ($this->hiddenField as $field) {
					
				if (array_key_exists($field, $data)) {
					unset($data[$field]);
				}

			} 
		}
		

		return $data;
	}

}
