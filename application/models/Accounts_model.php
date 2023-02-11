<?php

class Accounts_model extends CI_Model {

    protected $table = "ci_accounts";

    protected $primaryKey = 'ID';

    protected $softDeletes = TRUE;

    protected $passWordHash = 'account_pwd';

    protected $getProcessRule = [
        'is_disabled' => ['0' => '啟用', '1' => '停用'],
    ];

}


?>