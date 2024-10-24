<?php

namespace App\Models;

use App\System\Core\Models;

/**
 * @property string $id
 * @property string $name
 * @property string $type
 * @property string $laps
 * @property string $baseLapTime
 */
class Track extends Models
{
    protected string $table = 'track';
    protected string $pk = 'id';
}