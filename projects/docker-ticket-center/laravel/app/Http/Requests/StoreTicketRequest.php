<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTicketRequest extends FormRequest
{
    public const ACCEPTED_FIELDS = [
        'category',
        'domains',
        'organization',
        'title',
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
        $domainRegex = '/^([a-zA-Z0-9](?:(?:[a-zA-Z0-9-]*|(?<!-)\.(?![-.]))*[a-zA-Z0-9]+)?)$/';

        return [
            'category'          => 'required|string',
            'domains'           => 'required|array',
            'domains.*'         => ['required', 'regex:' . $domainRegex],
            'organization.name' => 'required|string',
            'organization.id'   => 'required|numeric',
            'title'             => 'required|string',
            'owner'             => 'string',
            'private'           => 'boolean',
            'message.content'   => 'required|string|max:2000',
            'status'            => [
                'in:'.implode(',',Ticket::STATUS_ARRAY),
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ], 422));
    }

}
