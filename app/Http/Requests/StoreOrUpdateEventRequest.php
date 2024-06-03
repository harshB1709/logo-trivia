<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrUpdateEventRequest extends FormRequest
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
        $slug_unique_rule = '|unique:events,slug';
        $slug_unique_rule .= $this->route()->getName() === 'update-event' ? ",{$this->event->id}" : '';
        return [
            'name' => 'required|string|max:70',
            'slug' => 'required|string|max:100' . $slug_unique_rule,
            'wordset_id' => 'required'
        ];
    }
}
