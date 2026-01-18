<?php

namespace App\Http\Requests;

use App\Rules\FileNameUpdateRule;
use App\Rules\FileNameUpdateUniqueRule;
use Illuminate\Foundation\Http\FormRequest;

class FileNameUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file_name_id'=>'required|numeric',
            'new_file_name'=>['required',new FileNameUpdateRule()]
        ];
    }
    public function messages(): array
    {
        return [
            'new_file_name.required'=>'Doküman ismi zorunludur',
        ];
    }
}
