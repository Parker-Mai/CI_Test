<?php

class Members_model extends CI_Model
{
    //設public是因為要給auth用 (暫時先這樣 找到解法再說)

    public $table = "ci_members";

    public $primaryKey = 'ID';

    public $loginField = 'member_name';

    public $passWordHash = 'member_pwd';

    public $hiddenField = ['member_pwd','last_login_ip','last_login_at','create_at','update_at'];

}

?>