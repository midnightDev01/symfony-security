<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory;
    use SoftDeletes;
    use AutoincrementTrait;

    public const NOTIFY_OWNER        = 'notify_owner';
    public const NOTIFY_PARTICIPANTS = 'notify_participants';

    protected $dates = ['deleted_at'];
    public $fillable = [
        'name',
        'notifications',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

}
