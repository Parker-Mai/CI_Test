<?php

class Accounts_model extends CI_Model {

    protected $table = "ci_accounts";

    protected $primaryKey = 'ID';

    public function login($inputData, $field)
    {

        $query = $this->db->get_where($this->table, array($field => $inputData[$field]));

        if($query->num_rows() != 1){

			return FALSE;

		} else {

            $dbData = $query->row_array();

            //密碼驗證
            if (!password_verify($inputData['account_pwd'], $dbData['account_pwd'])) {
                
                die('<script>alert("帳號或密碼錯誤。");history.back();</script>');

            }
            
            $this->load->library('session');

            $this->session->set_userdata([
                'userID'        => $dbData['ID'],
                'userName'      => $dbData['account_name'],
                'userRealName'  => $dbData['account_realname'],
            ]);

            return TRUE;

        }

        

    }

}


?>