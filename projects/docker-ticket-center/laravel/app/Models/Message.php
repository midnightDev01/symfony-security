<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'internal',
        'owner',
        'ticket_id',
        'content',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
