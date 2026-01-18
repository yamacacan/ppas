<?php

namespace App\Http\Requests;

use App\Rules\FileNameUpdateRule;
use Illuminate\Foundation\Http\FormRequest;

class FileRequest extends FormRequest
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
            'file_category'=>'required|gt:0',
            'file_name'=>['required','unique:App\Models\FileName,title'],
            'file'=>'required|file|max:10240|mimes:pdf,doc,docx,xlsx,xls'
        ];
    }
    public function messages(): array
    {
        return [
            'file_category.required'=>'Doküman türü zorunludur',
            'file_category.gt'=>'En az bir doküman türü seçiniz',
            'file_name.required'=>'Doküman ismi zorunludur',
            'file_name.unique'=>'Doküman ismi kayıltıdır.Başka bir isim deneyiniz',
            'file.required'=>'Doküman seçiniz',
            'file.file'=>'Doküman seçiniz',
            'file.max'=>'Doküman boyutu en fazla 10 MB olmalıdır',
            'file.mimes'=>'Doküman pdf,doc,docx,xls,xlsx türünde olmalıdır',
        ];
    }
}
