<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use MongoDB\Operation\FindOneAndUpdate;

trait AutoincrementTrait
{
    public function autoincrement(): void
    {
        $this->id = self::getID();
    }

    private static function getID()
    {
        $seq = DB::connection(env('DB_CONNECTION'))->getCollection('counters')->findOneAndUpdate(
            ['id' => self::class],
            ['$inc' => ['seq' => 1]],
            ['new' => true, 'upsert' => true, 'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER]
        );

        return $seq->seq;
    }
}
