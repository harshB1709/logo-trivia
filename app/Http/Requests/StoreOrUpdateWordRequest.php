<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrUpdateWordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:15',
            'points' => 'required|integer|between:1,3',
            'svg-file' => [
                'file|mimetypes:image/svg+xml|max:200',
                Rule::requiredIf(fn () => !$this->route('word'))
            ],
            'hint' => 'string|nullable'
        ];
    }
}
