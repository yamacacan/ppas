<?php

namespace App\Http\Controllers\Audit_Reports;

use Carbon\Carbon;
use App\Models\Entity;
use App\Models\Audits\Audit;

use App\Models\EntitySubGroup;

use App\Exports\AttachmentAExport;

use App\Exports\AttachmentBExport;
use App\Exports\AttachmentCExport;
use App\Exports\AttachmentEExport;
use App\Exports\AttachmentFExport;
use App\Exports\AttachmentGExport;
use App\Models\Audits\AuditEntity;
use Illuminate\Support\Facades\DB;
use App\Exports\AttachmentC2Export;
use App\Exports\AttachmentG2Export;
use App\Models\Audits\AuditAuditor;
use App\Models\Audits\AuditProgram;
use App\Http\Controllers\Controller;
use App\Models\Audits\AuditResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttachmentAExportPdf;
use App\Exports\AttachmentBExportPdf;
use App\Exports\AttachmentCExportPdf;
use App\Exports\AttachmentEExportPdf;
use App\Exports\AttachmentFExportPdf;
use App\Exports\AttachmentGExportPdf;
use App\Models\Audits\AuditPersonnel;
use App\Exports\AttachmentC2ExportPdf;
use App\Exports\AttachmentG2ExportPdf;
use App\Models\Precautions\Precaution;
use App\Models\EntitySubGroupPrecaution;
use App\Models\Audits\AuditProgramTarget;
use App\Models\Precautions\PrecautionTitle;
use App\Models\Audits\AuditApplicationProcess;
use App\Models\Precautions\PrecautionMainTitle;
use App\Models\Audits\AuditProgramTargetProcess;

class AuditReportController extends Controller
{

    public function index()
    {

        $audits = Audit::orderBy('id')->get();

        return  view('denetim_raporlari.index', compact('audits'));
    }

    public function exportAttachmentA($auditID, $exportType)
    {

        $audit = Audit::whereId($auditID)->first();
        $data = [
            ['EK - A : DENETİM EKİBİ BİLGİSİ '],
            ['Kurum Adı ', $audit->institution_name],
        ];

        switch ($audit->AuditorAssignmentTypes->id) {
            case 1:
                $data[] = ['Denetleyen ', $audit->AuditorAssignmentTypes->name];
                break;

            case 2:
                $data[] = ['Denetleyen ', 'Kurum Dışı Geçici Görevlendirme', 'Kurum Adı ', $audit->institution_name];
                break;

            case 3:
                $data[] = ['Denetleyen ', 'Firma', 'Firma Adı ', $audit->auditor];
                break;
        }


        $data[] = [
            ['Sıra No ', 'Denetim Rolü ', 'Adı ve Soyadı ', ' Görevlendirilme Türü ', 'Sertifika / Uzmanlık Alanı ']
        ];

        $auditAuditors = AuditAuditor::where('audit_id', $auditID)->get();
        foreach ($auditAuditors as $key => $auditAuditor) {
            $data[] = [
                ($key + 1) . ' ',
                $auditAuditor->auditorRoles->role,
                $auditAuditor->auditors->name . ' ' . $auditAuditor->auditors->last_name,
                $auditAuditor->AuditorAssignmentTypes->name,
                $auditAuditor->expertise

            ];
        }

        if ($exportType == 'excel') {

            return Excel::download(new AttachmentAExport($data),  $audit->institution_name . '_Denetim_Ekibi_Bilgisi_EK-A.xlsx');
        } else {

            return Excel::download(new AttachmentAExportPdf($data), $audit->institution_name . '_Denetim_Ekibi_Bilgisi_EK-A.pdf', \Maatwebsite\Excel\Excel::MPDF);
        }
    }

    public function exportAttachmentB($auditID, $exportType)
    {

        $audit = Audit::whereId($auditID)->first();
        $data = [
            ['EK – B: VARLIK GRUPLARI VE DENETİM KAPSAMI '],
            ['Denetim ekibi tarafından denetim kapsamına alınan varlıklar aşağıdaki tabloda kayıt altına alınmalıdır.'],
            ['#', 'Varlık Grubu Ana Başlığı', 'Varlık Grubu No', 'Varlık Sayısı', 'Varlık Grubu Adı', 'Kritiklik Derecesi 1 / 2 / 3', 'Denetim Kapsamında mı? Evet – E Hayır – H']
        ];


        $auditEntities = AuditEntity::where('audit_id',$auditID)->get();
        //dd($auditEntities);

        foreach ($auditEntities as $key => $auditEntity) {

            
            $entityCount = Entity::where('sub_group_id', $auditEntity->subGroup->id)->sum('quentity');

            $isScope = isset($auditEntity->subGroup->id) ? 'Evet' : 'Hayır';
            $data[] = [
                ($key + 1) . ' ',
                $auditEntity->subGroup->mainGroup->name,
                $auditEntity->subGroup->group_no,
                $entityCount . ' ',
                $auditEntity->subGroup->name,
                $auditEntity->subGroup->degree_of_criticality . ' ',
                $isScope,

            ];
        }

        if ($exportType == 'excel') {

            return Excel::download(new AttachmentBExport($data),  $audit->institution_name . '_Varlik_Gruplari_Ve_Denetim_Kapsami_EK-B.xlsx');
        } else {

            return Excel::download(new AttachmentBExportPdf($data), $audit->institution_name . '_Varlik_Gruplari_Ve_Denetim_Kapsami_EK-B.pdf', \Maatwebsite\Excel\Excel::MPDF);
        }
    }

    public function exportAttachmentC($auditID, $exportType)
    {

        $audit = Audit::whereId($auditID)->first();
        $auditProgram = AuditProgram::where('audit_id', $auditID)->get();
        //dd($auditProgram);
        if (!$auditProgram->isEmpty()) {

            $auditProgramTargets = AuditProgramTarget::get();
            $auditProgramTargetProcesses = AuditProgramTargetProcess::get();



            $data[] = ['Denetlenecek Süreç', 'Denetçi(ler) / Uzman(lar)', 'Bilgi Alınacak Birim / Personel	', 'Hazırlanması Gereken Bilgi, Belge, Doküman', 'Öngörülen Denetim Zamanı'];
            foreach ($auditProgramTargets as $key => $value) {
                $data[] = ['Hedef ' . ($key + 1) . ': ' . $value->target];
                foreach ($auditProgramTargetProcesses as $auditProgramTargetProcess) {
                    if ($value->id == $auditProgramTargetProcess->target_id) {

                        $auditProgram = AuditProgram::where('audit_id', $auditID)
                            ->where('process_id', $auditProgramTargetProcess->id)
                            ->first();

                        $auditor = $auditProgram->auditors->name . ' ' . $auditProgram->auditors->last_name;

                        $personnel = $auditProgram->personnels->user->name . ' ' . $auditProgram->personnels->user->last_name;

                        //dd( $auditor);


                        $data[] = [
                            $auditProgramTargetProcess->process,
                            $auditor,
                            $personnel,
                            $auditProgram->document,
                            Carbon::createFromFormat('Y-m-d', $auditProgram->audit_time)->format('d-m-Y'),
                        ];
                    }
                }
            }


            if ($exportType == 'excel') {

                return Excel::download(new AttachmentCExport($data),  $audit->institution_name . '_Denetim_Raporu_EK-C.xlsx');
            } else {

                return Excel::download(new AttachmentCExportPdf($data), $audit->institution_name . '_Denetim_Raporu_EK-C.pdf', \Maatwebsite\Excel\Excel::MPDF);
            }
        } else {
            return back()->with('warning', 'Öncelikle denetim programı oluşturunuz');
        }
    }

    public function exportAttachmentC2($auditID, $exportType)
    {

        $audit = Audit::whereId($auditID)->first();
        $data = [
            ['EK-C.2: VARLIK GRUBU VE KRİTİKLİK DERECESİ TANIMLAMA FORMU '],
            [
                '#',
                'Varlık Ana Grubu',
                'Varlık Alt Grubu',
                'Uygulama ve Teknoloji Alanlarına Yönelik Güvenlik Tedbirleri (Her varlık grubu için aşağıdaki başlıkların Uygulanabilir (U) / Uygulanabilir Değil (UD) olduğunu belirtiniz.)',
                '', '', '', '', '',
                'Sıkılaştırma Tedbirleri (Her varlık grubu için aşağıdaki başlıkların Uygulanabilir (U) / Uygulanabilir Değil (UD) olduğunu belirtiniz.))', '', '',
                'Kritiklik Derecesi 1 / 2 / 3',
            ],
            [
                '', '', '',
                'Kişisel Verilerin Güvenliği',
                'Anlık Mesajlaşma Güvenliği',
                'Bulut Bilişim Güvenliği',
                'Kripto Uygulamaları Güvenliği',
                'Kritik Altyapılar Güvenliği',
                'Yeni Geliştirmeler ve Tedarik',
                'İşletim Sistemi Sıkılaştırma Tedbirleri',
                'Veri Tabanı Sıkılaştırma Tedbirleri',
                'Sunucu Sıkılaştırma Tedbirleri',
                '',
            ],
        ];



        $entitySubGroups = EntitySubGroup::orderBy('id')->get();


        $precautionMainTitleIDs = PrecautionMainTitle::whereIn('security_precaution_type', [4, 5])->pluck('id');

        //dd($precautionMainTitleIDs);



        foreach ($entitySubGroups as $key => $entitySubGroup) {

            $status = [];

            array_push(
                $status,
                ($key + 1) . ' ',
                $entitySubGroup->mainGroup->group_no . ' ' . $entitySubGroup->mainGroup->name,
                $entitySubGroup->group_no . ' ' . $entitySubGroup->name,

            );

            //dd($precautionMainTitleIDs);
            foreach ($precautionMainTitleIDs as $key => $precautionMainTitleID) {

                $precautionTitles = PrecautionTitle::where('parent_id', $precautionMainTitleID)->with('precautions')->get();
                $count = 0;

                //dd($precautionTitles);

                foreach ($precautionTitles as $key => $precautionTitle) {

                    foreach ($precautionTitle->precautions as $key => $value) {


                        //$implementationStatuses[] = GapAnalysis::where('precaution_id', $value->id)->where('sub_group_id', $entitySubGroup->id)->pluck('precaution_implementation_status')->first();
                        $count += EntitySubGroupPrecaution::where('precaution_id', $value->id)->where('sub_group_id', $entitySubGroup->id)->count();
                    }
                }
                // dd($count);

                // if (count($implementationStatuses) == 0) {

                //     $status[] = 'UD';
                // } else {

                //     if (in_array("UD", $implementationStatuses)) {

                //         $status[] = 'UD';
                //     } else {

                //         $status[] = 'U';
                //     }
                // }


                if ($count == 0) {

                    $status[] = 'UD';
                } else {

                    $status[] = 'U';
                }
            }

            array_push(
                $status,

                $entitySubGroup->degree_of_criticality,
            );
            $data[] = $status;
        }


        if ($exportType == 'excel') {
            return Excel::download(new AttachmentC2Export($data),  $audit->institution_name . '_Varlık_Grubu_Ve_Kritiklik_Derecesi_Tanımlama_Formu_EK-C2.xlsx');
        } else {

            return Excel::download(new AttachmentC2ExportPdf($data), $audit->institution_name . '_Varlık_Grubu_Ve_Kritiklik_Derecesi_Tanımlama_Formu_EK-C2.pdf', \Maatwebsite\Excel\Excel::MPDF);
        }
    }

    public function exportAttachmentE($auditID, $exportType)
    {

        $audit = Audit::whereId($auditID)->first();

        $auditCountBefore = Audit::where('created_at', '<', $audit->created_at)->count();
        $position = $auditCountBefore + 1;

        $auditors = AuditAuditor::where('audit_id', $auditID)->take(4)->with('auditors', 'auditorRoles')->get();
        foreach ($auditors as $value) {
            $auditorNames[] = $value->auditors->name . ' ' . $value->auditors->last_name;
            $auditorRoles[] = $value->auditorRoles->role;
        }

        $personnels = AuditPersonnel::where('audit_id', $auditID)->take(4)->with('auditIRPersonnels')->get();
        foreach ($personnels as  $value) {
            $personnelNames[] = $value->auditIRPersonnels->name . ' ' . $value->auditIRPersonnels->last_name;
            $personnelTitles[] = $value->auditIRPersonnels->title;
        }
        //dd(isset($auditors[2]));

        $data = [
            ['EK – E: REHBER UYGULAMA SÜRECİ ETKİNLİK DURUMU '],
            ['Denetim yöntemleri alanında yer alan “Diğer” denetçinin aşağıdaki tabloda yer verilen denetim yöntemleri dışında kullandığı denetim yöntemini ifade etmektedir'],
            [''],
            ['Kurum Adı:  ', $audit->institution_name],
            ['Rehber Sürümü', $position . ' ', 'Tarih:', $audit->audit_date],
            ['Denetçi / Uzman Bilgisi'],
            ['', '1 ', '2 ', '3 ', '4 '],
            [
                'Denetim Rolü',
                isset($auditorRoles[0]) ? $auditorRoles[0] : '-',
                isset($auditorRoles[1]) ? $auditorRoles[1] : '-',
                isset($auditorRoles[2]) ? $auditorRoles[2] : '-',
                isset($auditorRoles[3]) ? $auditorRoles[3] : '-',
            ],
            [
                'Ad Soyad',
                isset($auditorNames[0]) ? $auditorNames[0] : '-',
                isset($auditorNames[1]) ? $auditorNames[1] : '-',
                isset($auditorNames[2]) ? $auditorNames[2] : '-',
                isset($auditorNames[3]) ? $auditorNames[3] : '-',
            ],
            ['Bilgi Alınan Kurum Personeli'],
            ['', '1 ', '2 ', '3 ', '4 '],
            [
                'Ünvan',
                isset($personnelTitles[0]) ? $personnelTitles[0] : '-',
                isset($personnelTitles[1]) ? $personnelTitles[1] : '-',
                isset($personnelTitles[2]) ? $personnelTitles[2] : '-',
                isset($personnelTitles[3]) ? $personnelTitles[3] : '-',
            ],
            [
                'Ad Soyad',
                isset($personnelNames[0]) ? $personnelNames[0] : '-',
                isset($personnelNames[1]) ? $personnelNames[1] : '-',
                isset($personnelNames[2]) ? $personnelNames[2] : '-',
                isset($personnelNames[3]) ? $personnelNames[3] : '-',
            ],
            [''],
            ['Soru No',    'Denetim Soruları', 'Denetim Yöntem(ler)i', 'Etkinlik Durumu', 'Bulgu'],

        ];

        $auditApplicationProcesses = AuditApplicationProcess::where('audit_id', $auditID)->with('questions')->get();
        foreach ($auditApplicationProcesses as $auditApplicationProcess) {
            $auditMethods = explode('+', $auditApplicationProcess->audit_method);
            $auditMethod = "";
            foreach ($auditMethods as $value) {

                $result = "";
                switch ($value) {
                    case 'M':
                        $result = 'Mülakat';
                        break;

                    case 'G':
                        $result = 'Gözden Geçirme';
                        break;

                    case 'GD':
                        $result = 'Güvenlik Denetimi';
                        break;
                    case 'S':
                        $result = 'Sızma Testi';
                        break;
                    case 'K':
                        $result = 'Kaynak Kod Analizi';
                        break;
                    case 'D':
                        $result = 'Diğer';
                        break;
                }
                $auditMethod .= '+' . $result;
            }

            $satus = '';
            switch ($auditApplicationProcess->activity_status) {
                case 'E':
                    $satus = 'Etkin';
                    break;

                case 'K':
                    $satus = 'Kısmen Etkin';
                    break;

                case 'ED':
                    $satus = 'Etkin Değil';
                    break;
            }


            $data[] = [
                $auditApplicationProcess->question_id . ' ',
                $auditApplicationProcess->questions->question,
                ltrim($auditMethod, '+'),
                $satus,
                $auditApplicationProcess->finding
            ];
        }


        if ($exportType == 'excel') {
            return Excel::download(new AttachmentEExport($data),  $audit->institution_name . '_Denetim_Uygulama_Sureci_Raporu_EK-E.xlsx');
        } else {

            return Excel::download(new AttachmentEExportPdf($data), $audit->institution_name . '_Denetim_Uygulama_Sureci_Raporu_EK-E.pdf', \Maatwebsite\Excel\Excel::MPDF);
        }
    }

    //Tedbir Etkinlik Durumu
    public function exportAttachmentF($auditID, $exportType)
    {

        $audit = Audit::whereId($auditID)->first();

        $auditCountBefore = Audit::where('created_at', '<', $audit->created_at)->count();
        $position = $auditCountBefore + 1;

        $auditors = AuditAuditor::where('audit_id', $auditID)->take(4)->with('auditors', 'auditorRoles')->get();
        foreach ($auditors as $value) {
            $auditorNames[] = $value->auditors->name . ' ' . $value->auditors->last_name;
            $auditorRoles[] = $value->auditorRoles->role;
        }

        $personnels = AuditPersonnel::where('audit_id', $auditID)->take(4)->with('auditIRPersonnels')->get();
        foreach ($personnels as  $value) {
            $personnelNames[] = $value->auditIRPersonnels->name . ' ' . $value->auditIRPersonnels->last_name;
            $personnelTitles[] = $value->auditIRPersonnels->title;
        }

        $data = [
            ['EK – F: TEDBİR ETKİNLİK DURUMU '],
            ['Denetim yöntemleri alanında yer alan “Diğer” denetçinin aşağıdaki tabloda yer verilen denetim yöntemleri dışında kullandığı denetim yöntemini ifade etmektedir'],
            [''],
            ['Kurum Adı:  ', $audit->institution_name],
            ['Rehber Sürümü', $position . ' ', 'Tarih:', $audit->audit_date],
            ['Denetçi / Uzman Bilgisi'],
            ['', '1 ', '2 ', '3 ', '4 '],
            [
                'Denetim Rolü',
                isset($auditorRoles[0]) ? $auditorRoles[0] : '-',
                isset($auditorRoles[1]) ? $auditorRoles[1] : '-',
                isset($auditorRoles[2]) ? $auditorRoles[2] : '-',
                isset($auditorRoles[3]) ? $auditorRoles[3] : '-',
            ],
            [
                'Ad Soyad',
                isset($auditorNames[0]) ? $auditorNames[0] : '-',
                isset($auditorNames[1]) ? $auditorNames[1] : '-',
                isset($auditorNames[2]) ? $auditorNames[2] : '-',
                isset($auditorNames[3]) ? $auditorNames[3] : '-',
            ],
            ['Bilgi Alınan Kurum Personeli'],
            ['', '1 ', '2 ', '3 ', '4 '],
            [
                'Ünvan',
                isset($personnelTitles[0]) ? $personnelTitles[0] : '-',
                isset($personnelTitles[1]) ? $personnelTitles[1] : '-',
                isset($personnelTitles[2]) ? $personnelTitles[2] : '-',
                isset($personnelTitles[3]) ? $personnelTitles[3] : '-',
            ],
            [
                'Ad Soyad',
                isset($personnelNames[0]) ? $personnelNames[0] : '-',
                isset($personnelNames[1]) ? $personnelNames[1] : '-',
                isset($personnelNames[2]) ? $personnelNames[2] : '-',
                isset($personnelNames[3]) ? $personnelNames[3] : '-',
            ],
            [''],
            [
                'Sıra No',
                'Varlık Ana Grubu',
                'Varlık Alt Grubu',
                'Tedbir',
                'Tedbirin Uygulanma Durumu',
                'Telafi Edici Kontrol No',
                'Tedbirin Etkinlik Durumu',
                'Denetim Yöntem(ler)i',
            ],

        ];


        $pas = DB::table('precaution_activity_statuses')
            ->leftjoin('gap_analyses', function ($join) {
                $join->on('precaution_activity_statuses.subgroup_id', '=', 'gap_analyses.sub_group_id')
                    ->on('precaution_activity_statuses.precaution_id', '=', 'gap_analyses.precaution_id');
            })
            ->leftjoin('audit_responses', function ($join) {
                $join->on('precaution_activity_statuses.subgroup_id', '=', 'audit_responses.sub_group_id')
                    ->on('precaution_activity_statuses.precaution_id', '=', 'audit_responses.precaution_id');
            })
            ->select(
                'precaution_activity_statuses.*',
                'gap_analyses.institutional_description',
                'gap_analyses.precaution_implementation_status',
                'gap_analyses.compensatory_control_form_id',
                'gap_analyses.work_package_no',
                'audit_responses.finding',
                'audit_responses.description',
            )
            ->orderBy('subgroup_id')
            ->where('precaution_activity_statuses.audit_id', $auditID)
            ->get();

        foreach ($pas as &$value) {
            $value->precaution = Precaution::find($value->precaution_id);
            $value->subGroup = EntitySubGroup::with('mainGroup')->find($value->subgroup_id);
        }


        foreach ($pas as $key => $value) {

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

            $activityStatus = '';
            switch ($value->precaution_activity_status) {
                case 'E':
                    $activityStatus = 'Etkin';
                    break;

                case 'K':
                    $activityStatus = 'Kısmen Etkin';
                    break;

                case 'ED':
                    $activityStatus = 'Etkin Değil';
                    break;
            }

            $auditMethods = explode('+', $value->audit_method);
            $auditMethod = "";
            foreach ($auditMethods as $item) {

                $result = "";
                switch ($item) {
                    case 'M':
                        $result = 'Mülakat';
                        break;

                    case 'G':
                        $result = 'Gözden Geçirme';
                        break;

                    case 'GD':
                        $result = 'Güvenlik Denetimi';
                        break;
                    case 'S':
                        $result = 'Sızma Testi';
                        break;
                    case 'K':
                        $result = 'Kaynak Kod Analizi';
                        break;
                    case 'D':
                        $result = 'Diğer';
                        break;
                }
                $auditMethod .= '+' . $result;
            }

            $data[] = [
                ($key + 1) . ' ',
                $value->subGroup->mainGroup->group_no . ' ' . $value->subGroup->mainGroup->name,
                $value->subGroup->group_no . ' ' . $value->subGroup->name,
                $value->precaution->precaution_no . ' ' . $value->precaution->name,
                $implementitationStatus,
                $value->compensatory_control_form_id ?? '-',
                $activityStatus,
                ltrim($auditMethod, '+'),

            ];
        }


        if ($exportType == 'excel') {
            return Excel::download(new AttachmentFExport($data),  $audit->institution_name . '_Tedbir_Etkinlik_Durumu_EK-F.xlsx');
        } else {

            return Excel::download(new AttachmentFExportPdf($data), $audit->institution_name . '_Tedbir_Etkinlik_Durumu_EK-F.pdf', \Maatwebsite\Excel\Excel::MPDF);
        }
    }

    public function exportAttachmentG($auditID, $exportType)
    {

        $audit = Audit::whereId($auditID)->first();

        $data[] = ['EK - G : Tedbir Etkinleri Bulgu Tablosu'];
        $data[] = ['#', 'Ana Varlık Grubu', 'Alt Varlık Grubu', 'Bulgu Kodu', 'Bulgu', 'İlgili Olduğu Tedbir Maddeleri'];

        $auditResponses = AuditResponse::where('audit_id', $auditID)->whereNotNull('finding')->get();
        foreach ($auditResponses as $key => $value) {
            $data[] = [
                ($key + 1) . ' ',
                $value->subGroup->mainGroup->group_no . ' ' . $value->subGroup->mainGroup->name,
                $value->subGroup->group_no . ' ' . $value->subGroup->name,
                $value->finding_code,
                $value->finding,
                $value->precaution->precaution_no,
            ];
        }

        //dd($data);


        if ($exportType == 'excel') {
            return Excel::download(new AttachmentGExport($data),  $audit->institution_name . '_Bulgu_Tablosu-EK_G.xlsx');
        } else {

            return Excel::download(new AttachmentGExportPdf($data), $audit->institution_name . '_Bulgu_Tablosu-EK_G.pdf', \Maatwebsite\Excel\Excel::MPDF);
        }
    }

    public function exportAttachmentG2($auditID, $exportType)
    {

        $audit = Audit::whereId($auditID)->first();

        $data[] = ['EK - G2 : Uygulama Süreci Bulgu Tablosu'];
        $data[] = ['#', 'Sorular', 'Bulgu Kodu', 'Bulgu'];

        $auditApplicationProcesses = AuditApplicationProcess::where('audit_id', $auditID)
        ->whereNotNull('finding')        
        ->with('questions')->get();

        foreach ($auditApplicationProcesses as $key => $value) {
            $data[] = [
                ($key + 1) . ' ',
                $value->questions->question,
                $value->finding_code. ' ',
                $value->finding,
            ];
        }

        //dd($data);


        if ($exportType == 'excel') {
            return Excel::download(new AttachmentG2Export($data),  $audit->institution_name . '_Bulgu_Tablosu-EK_G2.xlsx');
        } else {

            return Excel::download(new AttachmentG2ExportPdf($data), $audit->institution_name . '_Bulgu_Tablosu-EK_G2.pdf', \Maatwebsite\Excel\Excel::MPDF);
        }
    }
}
