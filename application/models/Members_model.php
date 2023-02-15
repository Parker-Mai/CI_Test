<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Members_model extends EX_Model
{
    protected $table = "ci_members";

    protected $primaryKey = 'ID';

    protected $loginField = 'member_name';

    protected $passWordHash = 'member_pwd';

    protected $hiddenField = ['member_pwd','last_login_ip','last_login_at','create_at','update_at'];

    public function pwdVerify($loginPwd, $dbData)
    {

        return password_verify($loginPwd, $dbData[$this->passWordHash]);

    }

}

?>