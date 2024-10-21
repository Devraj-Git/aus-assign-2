<?php

namespace App\Models;

use App\System\Core\Models;

/**
 * @property string $username
 * @property string $password
 * @property string $salt
 * @property string $user_describe
 * @property string $custom_data
 * @property string $role
 * @property string $discord_access_token
 */
class Users extends Models
{
    protected string $table = 'users';

    public function user_type() {
        return $this->relation(UsersType::class, 'users_type', 'role', 'parent', 'role');
    }
}