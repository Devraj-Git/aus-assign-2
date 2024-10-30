<?php

namespace App\Models;

use App\System\Core\Models;

/**
 * @property string $id
 * @property string $track
 * @property string $entrants
 * @property string $startingPositions
 */
class Race extends Models
{
    protected string $table = 'race';

    public function tracks() {
        return $this->relation(Track::class, 'track', 'id', 'child', 'track');
    }

    public function laps() {
        return $this->relation(Lap::class, 'lap', 'race_id', 'child', 'id');
    }
}