<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;


class Ticket extends Model
{
    use HasFactory;
    use SoftDeletes;
    use AutoincrementTrait;

    const STATUS_CLOSED_SUCCESS = "closed_successful";
    const STATUS_CLOSED_FAILED = "closed_unsuccessful";
    const STATUS_AUTO_CLOSE = "pending_auto_close";
    const STATUS_CUSTOMER_RESPONSE = "customer_response_required";
    const STATUS_MYRA_RESPONSE = "myra_response_required";
    const STATUS_OPEN = "open";
    const STATUS_REOPENED = "reopened";

    const STATUS_ARRAY = [
        Ticket::STATUS_OPEN,
        Ticket::STATUS_MYRA_RESPONSE,
        Ticket::STATUS_CUSTOMER_RESPONSE,
        Ticket::STATUS_AUTO_CLOSE,
        Ticket::STATUS_CLOSED_FAILED,
        Ticket::STATUS_CLOSED_SUCCESS,
        Ticket::STATUS_REOPENED,
    ];

    protected string $title = 'string';
    protected $dates = ['deleted_at'];
    public $fillable = [
        'category',
        'domains',
        'title',
        'status',
        'assignee',
        'priority',
        'message',
        'internal',
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function updateStatus($newStatus) {
        if(in_array($newStatus, self::STATUS_ARRAY)) {
            $this->status = $newStatus;
            return $this->save();
        }
        return false;
    }

    public function closed() {
        return in_array($this->status, [self::STATUS_CLOSED_SUCCESS, self::STATUS_CLOSED_FAILED]);
    }

}
