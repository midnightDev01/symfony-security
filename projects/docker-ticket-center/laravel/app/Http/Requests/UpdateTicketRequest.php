<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTicketRequest extends FormRequest
{
    public const ACCEPTED_FIELDS = [
        'assignee',
        'priority',
        'status',
    ];

    public function getAcceptedFields(): array
    {
        return $this->only(self::ACCEPTED_FIELDS);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function wantsJson()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'assignee'         => 'string',
            'priority'         => [
                'in:high, low, medium',
            ],
            'status'           => [
                'in:'.implode(',',Ticket::STATUS_ARRAY),
            ],
            'message.content'  => 'string',
            'message.internal' => 'boolean',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ], 422));
    }
}
