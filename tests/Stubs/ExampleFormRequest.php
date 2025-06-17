<?php

namespace Kroderdev\SchemaHelper\Tests\Stubs;

use Illuminate\Foundation\Http\FormRequest;

class ExampleFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email',
        ];
    }
}
