<?php

namespace App\Models;

use App\System\Core\Models;

/**
 * @property string $id
 * @property string $driver_number
 * @property string $car_number
 * @property string $race
 * @property string $street
 */
class Skills extends Models
{
    protected string $table = 'skill';

    // public function skills() {
    //     return $this->relation(UsersType::class, 'users_type', 'role', 'parent', 'role');
    // }
}