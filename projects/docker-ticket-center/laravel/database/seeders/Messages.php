<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\Ticket;
use Faker\Factory;
use Illuminate\Database\Seeder;

class Messages extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        $tickets = Ticket::all();
        foreach ($tickets as $ticket) {
            $messages = [];
            for ($x = 1; $x <= rand(1, 5); $x++) {
                $message           = new Message();
                $message->owner    = $faker->name();
                $message->content  = $faker->sentence(20, true);
                $message->internal = $faker->boolean(10);
                $messages[]        = $message;
            }
            $ticket->messages()->saveMany($messages);
            $ticket->save();
        }
    }
}
