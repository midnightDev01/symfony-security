<?php

namespace Database\Seeders;

use App\Models\AutoincrementTrait;
use App\Models\Organization;
use App\Models\Ticket;
use Faker\Factory;
use Illuminate\Database\Seeder;

class Tickets extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    use AutoincrementTrait;

    public function run()
    {
        $faker = Factory::create();
        $orgs  = ['Myra Security', 'Schwarz', 'DKB', 'Sparkasse', 'NRW'];

        for ($x = 1; $x <= 30; $x++) {
            $organization = Organization::where('id', random_int(1, 4))->first();
            $ticket       = new Ticket();
            $ticket->autoincrement();
            $ticket->title    = $faker->sentence(6, true);
            $ticket->category = $faker->randomElement(['sales', 'generic', 'support', 'improvement']);
            $ticket->priority = $faker->randomElement(['critical', 'high', 'medium', 'low']);
            $ticket->owner    = $faker->name();
            $ticket->assignee = $faker->name();
            $ticket->status   = $faker->randomElement(Ticket::STATUS_ARRAY);
            $ticket->private  = $faker->boolean(10);
            $domains          = [];
            for ($y = 0; $y <= rand(0, 3); $y++) {
                $domains[] = $faker->domainName;
            }
            $ticket->domains = $domains;
            $organization->tickets()->save($ticket);
        }
    }
}
