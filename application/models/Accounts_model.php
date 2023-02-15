<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts_model extends EX_Model
{
    protected $table = "ci_accounts"; 

    protected $primaryKey = 'ID';

    protected $softDeletes = TRUE;

    protected $loginField = 'account_name';

    protected $passWordHash = 'account_pwd';

    protected $hiddenField = ['account_pwd','last_login_ip','last_login_at','create_at','update_at'];

    protected $getProcessRule = [
        // 'is_disabled' => ['0' => '啟用', '1' => '停用'],
        // 'is_disabled' => ['0' => 'checked', '1' => ''],
    ];

    public function pwdVerify($loginPwd, $dbData)
    {

        return password_verify($loginPwd, $dbData[$this->passWordHash]);

    }

}

?>