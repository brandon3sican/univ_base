<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OfficeType;
use App\Models\Office;
use App\Models\RecordType;

class EssentialsSeeder extends Seeder
{
    public function run(): void
    {
        // Create Office Types
        $officeTypes = [
            ['name' => 'RO', 'desc' => 'Regional Office'],
            ['name' => 'PENRO', 'desc' => 'Provincial Environment and Natural Resources Office'],
            ['name' => 'CENRO', 'desc' => 'Community Environment and Natural Resources Office'],
        ];

        foreach ($officeTypes as $type) {
            OfficeType::firstOrCreate($type);
        }

        // Create Offices
        $offices = [
            ['name' => 'RO', 'office_types_id' => 1],
            ['name' => 'ABRA', 'office_types_id' => 2],
            ['name' => 'APAYAO', 'office_types_id' => 2],
            ['name' => 'BENGUET', 'office_types_id' => 2],
            ['name' => 'IFUGAO', 'office_types_id' => 2],
            ['name' => 'KALINGA', 'office_types_id' => 2],
            ['name' => 'MT.PROVINCE', 'office_types_id' => 2],
        ];

        foreach ($offices as $office) {
            Office::firstOrCreate(['name' => $office['name']], $office);
        }

        // Create Record Types
        $recordTypes = [
            ['name' => 'PROGRAM', 'desc' => 'Program level record'],
            ['name' => 'PROJECT', 'desc' => 'Project level record'],
            ['name' => 'MAIN ACTIVITY', 'desc' => 'Main activity record'],
            ['name' => 'SUB-ACTIVITY', 'desc' => 'Sub-activity record'],
            ['name' => 'SUB-SUB-ACTIVITY', 'desc' => 'Sub-sub-activity record'],
        ];

        foreach ($recordTypes as $type) {
            RecordType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
}
