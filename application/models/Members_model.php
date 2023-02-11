<?php

class Members_model extends CI_Model {

    protected $table = "ci_members";

    protected $primaryKey = 'ID';

    protected $passWordHash = 'member_pwd';

    public function login($inputData, $field)
    {
        
        $query = $this->db->get_where($this->table, array($field => $inputData[$field]));
        
        if($query->num_rows() != 1){

			return FALSE;

		} else {

            $dbData = $query->row_array();

            //密碼驗證
            if (!password_verify($inputData['member_pwd'], $dbData['member_pwd'])) {
                
                die('<script>alert("帳號或密碼錯誤。");history.back();</script>');

            }
            
            //資料放session
            $this->load->library('session');

            $this->session->set_userdata([
                'userID'        => $dbData['ID'],
                'userName'      => $dbData['member_name'],
                'userRealName'  => $dbData['member_realname'],
            ]);

            //更新登入IP、登入時間
            $datas['last_login_ip'] = $this->input->ip_address();
            $datas['last_login_at'] = date('Y/m/d H:i:s');

			$this->db->where($this->primaryKey, $dbData['ID']);
			$this->db->update($this->table, $datas);

            return TRUE;

        }

    }

}


?>