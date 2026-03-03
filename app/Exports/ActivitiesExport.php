<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ActivitiesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data['summaries'];
    }

    public function headings(): array
    {
        return [
            'Tarih',
            'Kullanıcı',
            'Tür',
            'Süre (MS)',
            'Süre (Saat)',
            'Aktivite Sayısı',
        ];
    }

    public function map($summary): array
    {
        $userName = $summary->computerUser->name ?? $summary->username;
        return [
            $summary->date->format('d.m.Y'),
            $userName . ' (' . $summary->username . ')',
            strtoupper($summary->category_type),
            $summary->total_duration_ms,
            round($summary->total_duration_ms / (1000 * 60 * 60), 2),
            $summary->activity_count,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
