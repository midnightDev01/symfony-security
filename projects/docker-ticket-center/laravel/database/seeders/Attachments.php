<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\Message;
use App\Models\Ticket;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use MongoDB\BSON\Binary;

class Attachments extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        $messages = Message::all();
        foreach ($messages as $message) {
            $rand = rand(1, 100);
            if ($rand < 80) {
                $attachment = new Attachment();
                $attachment->attachment = new Binary(file_get_contents($faker->image()), Binary::TYPE_GENERIC);
                $attachment->filetype = 'jpg';
                $attachment->filename = $faker->word() . ".png";
                $message->attachments()->save(
                    $attachment
                );
                $message->save();
            }
        }
    }
}
