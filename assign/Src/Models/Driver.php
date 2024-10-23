<?php

namespace App\Models;

use App\System\Core\Models;

/**
 * @property string $id
 * @property string $number
 * @property string $shortName
 * @property string $name
 * @property string $skill
 */
class Driver extends Models
{
    protected string $table = 'driver';

    public function skills() {
        return $this->relation(Skills::class, 'skill', 'driver_number', 'child', 'number');
    }

    public function getDriverWithSkills(int $driverNumber = null) {
        if ($driverNumber) {
            $drivers =  $this->where('number', $driverNumber)->get();
        } else {
            $drivers = $this->get();
        }
        $result = [];
        foreach ($drivers as $driver) {
            if ($driver) {
                $skills = (new Skills)->where('driver_number', $driver->number)->get();
                $skillData = [];
                foreach ($skills as $skill) {
                    $skillData = [
                        'race' => $skill->race,
                        'street' => $skill->street,
                    ];
                }
                $result[] = [
                    'number' => $driver->number,
                    'shortName' => $driver->shortName,
                    'name' => $driver->name,
                    'skill' => $skillData
                ];
            }
        }
        return $result;
    }
}