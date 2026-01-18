<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;


class AttachmentCExportPdf implements FromArray, WithEvents
{
    use Exportable;
    
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {

                $event->sheet->getDelegate()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $event->sheet->getDelegate()->getParent()->getProperties()->setTitle('EK - C Denetim Raporu');

                $event->sheet->getDelegate()->mergeCells('A2:E2');
                $event->sheet->getDelegate()->mergeCells('A12:E12');

                $event->sheet->getDelegate()->getStyle('A1:E1')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A2')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('B1')->getFont()->setBold(true);

                $event->sheet->getDelegate()->getStyle('A12')->getFont()->setBold(true);
                
                $event->sheet->getDelegate()->getStyle('G1')->getAlignment()->setWrapText(true);


                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(120);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(25);
               

                $highestRow = $event->sheet->getHighestRow();

                for ($rowNumber = 1; $rowNumber <= $highestRow; $rowNumber++) {
                    $cellRange = 'A'.$rowNumber.':'.'E'.$rowNumber; // Dinamik olarak hücre aralığını belirle
                    $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ]
                    ]);
                }




            }
        ];

    }
}
