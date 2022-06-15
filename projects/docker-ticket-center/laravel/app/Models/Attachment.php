<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $hidden = ['attachment'];
    protected $fillable = [
        'filename',
        'filetype',
        'attachment',
        'message_id',
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
