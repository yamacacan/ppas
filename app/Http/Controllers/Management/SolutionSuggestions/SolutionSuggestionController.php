<?php

namespace App\Http\Controllers\Management\SolutionSuggestions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Audits\Audit;
use App\Models\Audits\AuditQuestion;
use App\Models\Precautions\Precaution;
use Illuminate\Validation\Rules\Exists;

class SolutionSuggestionController extends Controller
{


    public function index()
    {

        return view('yonetimsel-islemler.cozum_onerileri.import');
    }

    public function exportSolutions()
    {

        $precautions = Precaution::orderBy('parent_id')->get();

        // CSV formatına çevir
        $csvHeader = ['ID', 'Çözüm Önerisi', 'Güncelleme Tarihi'];

        $csvData = $precautions->map(function ($precaution) {
            return [
                $precaution->id,
                $precaution->solution_suggestion,
                $precaution->updated_at,
            ];
        });

        // CSV dosyası oluştur
        $fileName = 'cozum_onerileri_' . date('Y_m_d_H_i_s') . '.csv';

        $handle = fopen(storage_path($fileName), 'w');
        fputcsv($handle, $csvHeader);

        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        // Dosyayı indir
        return response()->download(storage_path($fileName))->deleteFileAfterSend(true);
    }

    public function importSolutions(Request $request)
    {

        // Dosyanın varlığını kontrol et
        if (!$request->hasFile('csv_file')) {
            return back()->with('error', 'Lütfen bir CSV dosyası yükleyin.');
        }

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        // Başlıkları atla
        $header = fgetcsv($handle);

        $conflicts = []; // Çakışan kayıtları toplamak için dizi

        while (($data = fgetcsv($handle, 1000, ',')) !== false) {

            //sorular tablosunda aynı soru var mı kontrol et
            // $existing = AuditQuestion::where('precaution_id', $data[1])
            // ->where('question', $data[2])
            // ->first();

            $existing=Precaution::where('id', $data[0])->first();

            //Eğer aktarılan dosyada çözüm önerisi varsa ve mevcut kayıtta çözüm önerisi varsa
            if ($existing['solution_suggestion'] && $data[1]!=$existing['solution_suggestion']) {

                // Mevcut içerikle yeni içeriği birleştir
                $updatedContent = $existing['solution_suggestion'] . "\n" . $data[1];

                 // Çakışan kayıtları logla
                 $conflicts[] = [
                    'precaution_id' => "{$existing->precaution_no} {$existing->name}",
                    'old_content' => $existing->solution_suggestion,
                    'new_content' => $updatedContent,
                ];

                $existing->update([
                    'solution_suggestion' => $updatedContent,
                    'updated_at' => now(),
                ]);

               
            } else if(!$existing['solution_suggestion']&&$data[1]!="") {
                
                // Eğer çözüm önerisi yoksa ilgili kaydı güncelle
               $existing->update([
                    'solution_suggestion' => $data[1],
                    'updated_at' => now(),
                ]);
                
            }

            
        }

        fclose($handle);

        return back()
            ->with('success', 'Çözüm önerileri başarıyla içe aktarıldı.')
            ->with('conflicts', $conflicts);
    }
}
