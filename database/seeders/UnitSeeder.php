<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        Unit::create(['name' => 'Tablet', 'symbol' => 'tab']);
        Unit::create(['name' => 'Strip', 'symbol' => 'strp']);
        Unit::create(['name' => 'Botol', 'symbol' => 'btl']);
        Unit::create(['name' => 'Tube', 'symbol' => 'tube']);
        Unit::create(['name' => 'Sachet', 'symbol' => 'sch']);
        Unit::create(['name' => 'Box', 'symbol' => 'box']);
        Unit::create(['name' => 'Pcs', 'symbol' => 'pcs']);
    }
}