<?php

class Accounts_model extends CI_Model
{
    //設public是因為要給auth用 (暫時先這樣 找到解法再說)

    public $table = "ci_accounts"; 

    public $primaryKey = 'ID';

    protected $softDeletes = TRUE;

    public $loginField = 'account_name';

    public $passWordHash = 'account_pwd';

    public $hiddenField = ['account_pwd','last_login_ip','last_login_at','create_at','update_at'];

    protected $getProcessRule = [
        'is_disabled' => ['0' => '啟用', '1' => '停用'],
    ];

}

?>