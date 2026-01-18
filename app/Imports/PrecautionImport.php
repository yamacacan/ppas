<?php

namespace App\Imports;

use App\Models\Entity;
use App\Models\EntitySubGroup;
use App\Models\EntityMainGroup;


use Illuminate\Support\Collection;
use App\Models\Precautions\Precaution;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\Precautions\PrecautionTitle;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\Precautions\PrecautionMainTitle;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class PrecautionImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas, WithValidation
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
      
        if(count($rows)==0){
            $errors[]= 'Şablon boş lütfen indirdiğiniz şablonu doldurun';
        }
       
        foreach ($rows as $key=>$row) {

            

            if (isset($row['tedbir_ana_basligi'])&&isset($row['tedbir_basligi'])) {
                
                $precautionMainTitle=PrecautionMainTitle::where('name',$row['tedbir_ana_basligi'])->pluck('id');
                $precautionTitleID=PrecautionTitle::where('title',$row['tedbir_basligi'])->pluck('id');

                if(count($precautionMainTitle)==0){

                    $errors[]= ($key+2) .'. Satırda ki Tedbir Ana Başlığı kayıtlı değil';

                }elseif(count($precautionTitleID)==0){

                    $errors[]= ($key+2) .' Satırdaki Tedbir Başlığı kayıtlı değil';

                }else{

                    $precautionNos=Precaution::where('parent_id',$precautionTitleID)->latest("id")->first();
                    $precautionTitleNo=PrecautionTitle::where('id',$precautionTitleID)->pluck('title_no');
                    
                    if($precautionNos&&$precautionTitleNo){

                        $resultString = str_replace($precautionTitleNo[0], "", $precautionNos["precaution_no"]);
                        $newTitleNo=$precautionTitleNo[0].".".ltrim($resultString,".")+1;
                
                        $data = [
                            'parent_id' => $precautionTitleID[0],
                            'precaution_no' => $newTitleNo,
                            'name' => $row['tedbir_adi'],
                            'description' => $row['aciklama'],
                            'level' => $row['tedbir_seviyesi'],
            
                        ];

                    }else{
                        $errors[]= ($key+2) .'. Satırdaki data oluşturulamadı';
                    }
                

                    
                }

                              


              
            }else{
                $errors[]= ($key+2). ' Numaralı satırı kontrol ediniz';
            }

            

            
        }

        if(isset($errors)){

            Session::flash('warning', $errors);
        }else{

            foreach ($rows as $key=>$row) {

                $precautionTitleID=PrecautionTitle::where('title',$row['tedbir_basligi'])->pluck('id');
                    
                $precautionNos=Precaution::where('parent_id',$precautionTitleID)->latest("id")->first();

                $precautionTitleNo=PrecautionTitle::where('id',$precautionTitleID)->pluck('title_no');

                $resultString = str_replace($precautionTitleNo[0], "", $precautionNos["precaution_no"]);
                $newTitleNo=$precautionTitleNo[0].".".ltrim($resultString,".")+1;
                
                $data2 = [
                    'parent_id' => $precautionTitleID[0],
                    'precaution_no' => $newTitleNo,
                    'name' => $row['tedbir_adi'],
                    'description' => $row['aciklama'],
                    'level' => $row['tedbir_seviyesi'],
    
                ];

                //Doğrulama kurallarını kullanarak kaydetme işlemi
                $validator = Validator::make($data2, Precaution::$rules);
        
                if ($validator->fails()) {

                    $errors[]= ($key+2) .'. Satırdaki tedbir zaten kayıtlı';
                    Session::flash('warning', $errors);
                    
                }else{
                    Precaution::create($data2);
                    Session::flash('message', 'Tedbirler başarılı bir şekilde eklendi');  

                }

                             
                
            }

        }

        

       

       
    }

    public function rules(): array
    {
        return [

            //Excel alanları
            'tedbir_ana_basligi' => 'required',
            'tedbir_basligi' => 'required',
            'tedbir_adi' => 'required',
            'aciklama' => 'required',
            'tedbir_seviyesi' => 'required',

        ];
    }
}
