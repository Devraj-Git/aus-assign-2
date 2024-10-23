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

    public function getcarWithSuitability(int $carId = null) {
        if ($carId) {
            $cars =  $this->where('id', $carId)->get();
        } else {
            $cars = $this->get();
        }
        $result = [];
        foreach ($cars as $car) {
            if ($car) {
                $skills = (new Skills)->where('car_number', $car->id)->get();
                $skillData = [];
                foreach ($skills as $skill) {
                    $skillData = [
                        'race' => $skill->race,
                        'street' => $skill->street
                    ];
                }
                $driverData = null;
                if($car->driver){
                    $driverData = [];
                    $drivers = (new driver)->where('number', $car->driver)->get();
                    foreach ($drivers as $driver) {
                        $driverData = [
                            'name' => $driver->shortName,
                            'uri' => url('driver/'.$driver->number)
                        ];
                    }
                }
                $result[] = [
                    'id' => $car->id,
                    'driver' => $driverData,
                    'suitability' => $skillData,
                    'reliability' => $car->reliability,
                ];
            }
        }
        return $result;
    }
}