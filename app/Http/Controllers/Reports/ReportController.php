<?php

namespace App\Http\Controllers\Reports;

use App\Exports\EntitiesExport;
use App\Models\Entity;
use App\Models\GapAnalysis;
use Illuminate\Http\Request;
use App\Models\EntitySubGroup;
use App\Exports\GapAnlysisExportPdf;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\EntitySubGroupPrecaution;

set_time_limit(120); // 60 saniyeye çıkar

class ReportController extends Controller
{


    //Boşluk Analizi Raporu
    public function exportGapAnalysis($subGroupId,$exportType)
    {

        $subGroup=EntitySubGroup::find($subGroupId);
       
        $title=$subGroup->name;

        $data = [
            ['BOŞLUK ANALİZİ - '.$title],
            [''],
            [
                'Sıra No',
                'Varlık Ana Grubu',
                'Varlık Alt Grubu',
                'Tedbir',
                'Kurumsal Açıklama',
                'Tedbirin Uygulanma Durumu',
                'Telafi Edici Kontrol No',
                'Hedeflenen Durum',
                'İş Paketi No',
            ],

        ];


        $entitySubGroupPrecautions = EntitySubGroupPrecaution::where('sub_group_id', $subGroupId)->get();
        //dd($subGroupID);

        $precautionIDs = [];
        foreach ($entitySubGroupPrecautions as $entitySubGroupPrecaution) {
            $precautionIDs[] = $entitySubGroupPrecaution->precaution_id;
        }
    

        $gapAnalyses = GapAnalysis::where('sub_group_id', $subGroupId)
            ->whereIn('precaution_id', $precautionIDs)
            ->get();
       
        // $gapAnalyses = GapAnalysis::with('subGroup', 'precaution')
        //     ->where('sub_group_id', $subGroupId)
        //     ->get();
        //dd($gapAnalyses);

        foreach ($gapAnalyses as $key => $value) {

            $implementitationStatus = '';
            switch ($value->precaution_implementation_status) {
                case 'U':
                    $implementitationStatus = 'Uygulandı';
                    break;

                case 'K':
                    $implementitationStatus = 'Kısmen Uygulandı';
                    break;

                case 'Ç':
                    $implementitationStatus = 'Çoğunlukla Uygulandı';
                    break;
                case 'T':
                    $implementitationStatus = 'Telafi Edici Kontrol Uygulandı';
                    break;
                case 'Y':
                    $implementitationStatus = 'Uygulanmadı';
                    break;
                case 'UD':
                    $implementitationStatus = 'Uygulanabilir Değil';
                    break;
            }

            $targetedState = '';
            switch ($value->targeted_state) {
                case 'U':
                    $targetedState = 'Uygulandı';
                    break;

                case 'K':
                    $targetedState = 'Kısmen Uygulandı';
                    break;

                case 'Ç':
                    $targetedState = 'Çoğunlukla Uygulandı';
                    break;
                case 'T':
                    $targetedState = 'Telafi Edici Kontrol Uygulandı';
                    break;
                case 'Y':
                    $targetedState = 'Uygulanmadı';
                    break;
                case 'UD':
                    $targetedState = 'Uygulanabilir Değil';
                    break;
            }

            $data[] = [
                ($key + 1) . ' ',
                $value->subGroup->mainGroup->group_no . ' ' . $value->subGroup->mainGroup->name,
                $value->subGroup->group_no . ' ' . $value->subGroup->name,
                $value->precaution->precaution_no . ' ' . $value->precaution->name,
                $value->institutional_description,
                $implementitationStatus ,
                $value->compensatory_control_form_id,
                $targetedState,
                $value->work_package_no,
            ];
        }

        

        if ($exportType == 'excell') {
            return Excel::download(new GapAnlysisExportPdf($data), 'Boşluk_Analizi_'.$subGroup->name.'.xlsx');
        } else {

            return Excel::download(new GapAnlysisExportPdf($data), 'Boşluk_Analizi_'.$subGroup->name.'.pdf', \Maatwebsite\Excel\Excel::MPDF);
        }
    }

    //Varlıklar Raporu
    public function exportEntities()
    {
        $entities=Entity::all();

        $data=[

            ['Varlıklar'],
            [''],
            ['Varlık ID','Varlık','Ana Grubu','Alt Grubu','Varlık Lokasyonu','Açıklama',
            'Gizlilik','Bütünlük','Erişebilirlik','Kritiklik Derecesi','Varlık Sayısı','Varlık Sahibi'],
        ];

        foreach ($entities as $key => $value) {
            $data[]=[
                $value->entity_id,
                $value->name,
                $value->subGroup->mainGroup->group_no.' '.$value->subGroup->mainGroup->name,
                $value->subGroup->group_no.''.$value->subGroup->name,
                $value->location,
                $value->description,
                $value->gizlilik,
                $value->butunluk,
                $value->erisebilirlik,
                $value->degree_of_criticality,
                $value->quentity,
                $value->entity_owner,

            ];
        }


      
            return Excel::download(new EntitiesExport($data), 'Tüm Varlıklar.xlsx');
     

    }
}
