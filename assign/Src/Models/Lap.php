<?php

namespace App\Models;

use App\System\Core\Models;

/**
 * @property string $id
 * @property string $race_id
 * @property string $number
 * @property string $entrant
 * @property string $time
 * @property string $randomness
 * @property string $crashed
 */
class Lap extends Models
{
    protected string $table = 'lap';
}