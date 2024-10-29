<?php

namespace App\Models;

use App\System\Core\Models;

/**
 * @property string $id
 * @property string $track
 * @property string $entrants
 * @property string $startingPositions
 * @property string $laps
 */
class Race extends Models
{
    protected string $table = 'race';

    public function tracks() {
        return $this->relation(Track::class, 'track', 'id', 'child', 'track');
    }
}