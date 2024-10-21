<?php

namespace App\Models;

use App\System\Core\Models;

/**
 * @property string $id
 * @property string $track
 * @property string $entrant
 * @property string $startingPositions
 * @property string $laps
 */
class Race extends Models
{
    protected string $table = 'race';
}