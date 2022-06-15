<?php

namespace Database\Seeders;

use App\Models\AutoincrementTrait;
use App\Models\Organization;
use Faker\Factory;
use Illuminate\Database\Seeder;

class Organizations extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    use AutoincrementTrait;

    public function run()
    {
        $orgs  = ['Myra Security', 'Schwarz', 'DKB', 'Sparkasse', 'NRW'];
        $faker = Factory::create();

        for ($x = 1, $xMax = count($orgs); $x < $xMax; $x++) {

            $alias = $faker->boolean(50) ? $faker->email : '';

            $notifications = [];
            $organization  = new Organization();
            $organization->autoincrement();
            $organization->name = $orgs[$x];
            if ($alias) {
                $notifications['alias'] = $alias;
            }
            if ($x % 2 === 0) {
                $notifications['notify'] = Organization::NOTIFY_OWNER;
            } else {
                $notifications['notify'] = Organization::NOTIFY_PARTICIPANTS;
            }

            $organization->notifications = $notifications;
            $organization->save();
        }
    }
}
