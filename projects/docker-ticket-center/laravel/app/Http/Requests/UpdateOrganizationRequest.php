<?php

namespace App\Http\Requests;

use App\Models\Organization;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateOrganizationRequest extends FormRequest
{
    public const ACCEPTED_FIELDS = [
        'notifications',
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
            'notifications.alias'  => 'email',
            'notifications.notify' => 'required|in:' . Organization::NOTIFY_PARTICIPANTS . ',' . Organization::NOTIFY_OWNER,
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ], 422));
    }
}
