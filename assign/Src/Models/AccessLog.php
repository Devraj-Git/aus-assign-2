<?php

namespace App\Models;

use App\System\Core\Models;

/**
 * @property string $user_id
 * @property string $action
 * @property string $ip_address
 * @property string $identifier
 */
class AccessLog extends Models
{
    protected string $table = 'access_logs';

    public function user() {
        try{
            return $this->relation(Users::class, 'users', 'user_id', 'parent');
        }
        catch(\Exception $e){
            return null;
        }
    }
}