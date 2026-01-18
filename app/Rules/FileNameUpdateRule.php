<?php

namespace App\Rules;

use App\Models\FileName;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\DataAwareRule;

class FileNameUpdateRule implements ValidationRule, DataAwareRule
{
    protected $data = [];
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $file_name=FileName::where('title',$value)->first();
        if(isset($file_name->id))
            {
                if ($file_name->id!=$this->data['file_name_id']) {
                    $fail('Doküman başlığı kayıtlıdır');
                }
            }
    }

    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }
}
