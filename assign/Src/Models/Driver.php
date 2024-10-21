<?php

namespace App\Models;

use App\System\Core\Models;

/**
 * @property string $id
 * @property string $shortName
 * @property string $name
 * @property string $skill
 */
class Driver extends Models
{
    protected string $table = 'driver';

    // public function user_type() {
    //     return $this->relation(UsersType::class, 'users_type', 'role', 'parent', 'role');
    // }
}