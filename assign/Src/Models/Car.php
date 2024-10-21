<?php

namespace App\Models;

use App\System\Core\Models;

/**
 * @property string $id
 * @property string $driver
 * @property string $suitability
 * @property string $reliability
 */
class Car extends Models
{
    protected string $table = 'car';

    // public function user() {
    //     try{
    //         return $this->relation(Users::class, 'users', 'user_id', 'parent');
    //     }
    //     catch(\Exception $e){
    //         return null;
    //     }
    // }
}