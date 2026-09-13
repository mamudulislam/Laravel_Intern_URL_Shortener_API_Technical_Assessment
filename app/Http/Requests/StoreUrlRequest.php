<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUrlRequest extends FormRequest
{
    public function authorize(): bool { return $this->user() !== null; }

    public function rules(): array
    {
        return [
            'url' => ['required', 'url:http,https', 'max:2048'],
            'custom_code' => ['nullable', 'string', 'alpha_dash', 'min:3', 'max:32', Rule::unique('urls', 'short_code')],
        ];
    }
}
