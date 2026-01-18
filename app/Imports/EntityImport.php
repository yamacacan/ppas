<?php

namespace App\Imports;

use Exception;
use App\Models\Entity;
use App\Models\EntitySubGroup;
use App\Models\EntityMainGroup;

use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class EntityImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas, WithValidation
{
    /**
     * @param Collection $collection
     */
    // public function collection(Collection $rows)
    // {



    //     /// 

    //     DB::beginTransaction(); // Transaction başlat

    //     $errors = [];
    //     $validData = [];
    //     $existingIds = Entity::pluck('entity_id')->toArray(); // Veritabanındaki mevcut entity_id'ler

    //     //Varlık gruplarını ekleme
    //     foreach ($rows as $key => $row) {

    //         if (isset($row['varlik_ana_grubu'])) {

    //             $main_group = EntityMainGroup::where('name', $row['varlik_ana_grubu'])->first();

    //             if ($main_group) {

    //                 $sorgu = EntitySubGroup::where('group_no', 'LIKE', $main_group->group_no . '%')
    //                     ->orderBy('group_order_no', 'desc')
    //                     ->first();

    //                 if ($sorgu) {
    //                     $last_group_order_no = $sorgu->group_order_no;
    //                 } else {
    //                     $last_group_order_no = 0;
    //                 }

    //                 $data = [
    //                     'main_group_id' => $main_group->id,
    //                     'name' => $row['varlik_alt_grubu'],
    //                     'group_no' => $main_group->group_no . '.' . $last_group_order_no + 1,
    //                     'group_order_no' => $last_group_order_no + 1,
    //                     'degree_of_criticality' => $row['kritiklik_derecesi'],

    //                 ];

    //                 // Doğrulama kurallarını kullanarak kaydetme işlemi
    //                 $validator = Validator::make($data, EntitySubGroup::$rules);

    //                 if (!$validator->fails()) {

    //                     EntitySubGroup::create($data);
    //                 }
    //             } else {
    //                 $errors[] = $key . '. Satır ' . $row['varlik_ana_grubu'] . ' varlık grubu';
    //             }
    //         }
    //     }


    //     foreach ($rows as $row) {
    //         $sub_group = EntitySubGroup::where('name', $row['varlik_alt_grubu'])->first();

    //         if ($sub_group) {
    //             $data2 = [
    //                 'sub_group_id' => $sub_group->id,
    //                 'entity_id' => $row['varlik_id'],
    //                 'name' => $row['varlik_adi'],
    //                 'location' => $row['lokasyonu'],
    //                 'description' => $row['aciklama'],
    //                 'gizlilik' => $row['gizlilik'],
    //                 'butunluk' => $row['butunluk'],
    //                 'erisebilirlik' => $row['erisebilirlik'],
    //                 'entity_value' => $row['varlik_degeri'],
    //                 'degree_of_criticality' => $row['kritiklik_derecesi'],
    //                 'quentity' => $row['miktar'],
    //                 'entity_owner' => $row['varlik_sahibi'],
    //             ];

    //             // Aynı entity_id kontrolü (veritabanında ve geçici dizide)
    //             if (in_array($row['varlik_id'], $existingIds)) {
    //                 $errors[] = "Bu varlık zaten mevcut Varlık ID : {$row['varlik_id']}";
    //                 continue;
    //             }

    //             // Validasyon
    //             $validator = Validator::make($data2, Entity::$rules);

    //             if ($validator->fails()) {
    //                 // Validasyon hatalarını topla
    //                 $errors[] = $validator->errors()->first();
    //             } else {
    //                 // Geçerli veriyi toplama
    //                 $validData[] = $data2;
    //                 $existingIds[] = $row['varlik_id']; // Geçici ID'ye ekle
    //             }
    //         } else {
    //             $errors[] = "Lütfen şablonu kontrol ediniz: {$row['varlik_alt_grubu']}";
    //         }
    //     }

    //     // Hataları kontrol et
    //     if (!empty($errors)) {
    //         DB::rollBack(); // İşlemi geri al
    //         Log::error("Varlık ekleme hataları: ", $errors);
    //         return back()->withErrors($errors); // Hataları geri döndür
    //     }

    //     // Verileri kaydet
    //     foreach ($validData as $data) {
    //         Entity::create($data);
    //     }


    //     // Başarılı işlem
    //     DB::commit(); // İşlemi onayla
    //     Session::flash('message', 'Tüm varlıklar başarıyla eklendi');
    //     return back();
    // }


    public function collection(Collection $rows)
    {
        DB::beginTransaction(); // Transaction başlat

        try {
            $errors = [];
            $validData = [];
            $existingIds = Entity::pluck('entity_id')->toArray(); // Veritabanındaki mevcut entity_id'ler

            // Varlık gruplarını ekleme
            foreach ($rows as $key => $row) {
                if (isset($row['varlik_ana_grubu'])) {
                    $main_group = EntityMainGroup::where('name', $row['varlik_ana_grubu'])->first();
    
                    if ($main_group) {
                        // Varlık alt grubu benzersizlik kontrolü
                        $existingSubGroup = EntitySubGroup::where('name', $row['varlik_alt_grubu'])
                            ->where('main_group_id', $main_group->id)
                            ->first();
    
                        if (!$existingSubGroup) {
                            $sorgu = EntitySubGroup::where('group_no', 'LIKE', $main_group->group_no . '%')
                                ->orderBy('group_order_no', 'desc')
                                ->first();
    
                            $last_group_order_no = $sorgu ? $sorgu->group_order_no : 0;
    
                            // Yeni grup ekle
                            EntitySubGroup::create([
                                'main_group_id' => $main_group->id,
                                'name' => $row['varlik_alt_grubu'],
                                'group_no' => $main_group->group_no . '.' . ($last_group_order_no + 1),
                                'group_order_no' => $last_group_order_no + 1,
                                'degree_of_criticality' => $row['kritiklik_derecesi'],
                            ]);
                        }
                    } else {
                        $errors[] = $key . '. Satır ' . $row['varlik_ana_grubu'] . ' varlık grubu bulunamadı.';
                    }
                }
            }

            // Varlık ekleme
            foreach ($rows as $row) {
                $sub_group = EntitySubGroup::where('name', $row['varlik_alt_grubu'])->first();

                if ($sub_group) {
                    $data2 = [
                        'sub_group_id' => $sub_group->id,
                        'entity_id' => $row['varlik_id'],
                        'name' => $row['varlik_adi'],
                        'location' => $row['lokasyonu'],
                        'description' => $row['aciklama'],
                        'gizlilik' => $row['gizlilik'],
                        'butunluk' => $row['butunluk'],
                        'erisebilirlik' => $row['erisebilirlik'],
                        'entity_value' => $row['varlik_degeri'],
                        'degree_of_criticality' => $row['kritiklik_derecesi'],
                        'quentity' => $row['miktar'],
                        'entity_owner' => $row['varlik_sahibi'],
                    ];

                    // Aynı entity_id kontrolü
                    if (in_array($row['varlik_id'], $existingIds)) {
                        $errors[] = "Bu varlık zaten mevcut: Varlık ID: {$row['varlik_id']}";
                        continue;
                    }

                    // Validasyon
                    $validator = Validator::make($data2, Entity::$rules);

                    if ($validator->fails()) {
                        $errors[] = $validator->errors()->first();
                    } else {
                        $validData[] = $data2;
                        $existingIds[] = $row['varlik_id']; // Geçici ID'ye ekle
                    }
                } else {
                    $errors[] = "Alt grup bulunamadı: {$row['varlik_alt_grubu']}";
                }
            }

            // Hataları kontrol et
            if (!empty($errors)) {
                throw new Exception("Hatalar oluştu: " . implode(", ", $errors));
            }

            // Verileri kaydet
            foreach ($validData as $data) {
                Entity::create($data);
            }

            DB::commit(); // İşlemi onayla
            Session::flash('message', 'Tüm varlıklar ve gruplar başarıyla eklendi');
            return back();
        } catch (Exception $e) {
            DB::rollBack(); // İşlemi geri al
            Log::error("Varlık ekleme hatası: " . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }




    public function rules(): array
    {
        return [

            //Excel alanları
            'varlik_id' => 'unique:entities,entity_id',
            'varlik_adi' => 'required',

            'gizlilik' => 'required|numeric',
            'butunluk' => 'required|numeric',
            'erisebilirlik' => 'required|numeric',
            'varlik_degeri' => 'required|numeric',
            'kritiklik_derecesi' => 'required|numeric|between:1,3',
            'miktar' => 'required|numeric',
            'varlik_sahibi' => 'required',

            //'group_no'=>'unique',
            // 'main_group_id'=>'required',
            // 'name'=>'required:entities',
            // 'degree_of_criticality'=>'required',


        ];
    }
}
