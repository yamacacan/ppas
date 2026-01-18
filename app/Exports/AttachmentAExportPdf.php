<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use PhpOffice\PhpSpreadsheet\Style\Border;


class AttachmentAExportPdf implements FromArray, WithEvents
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
              
                //dd($event->sheet->getDelegate());
                $event->sheet->getDelegate()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $event->sheet->getDelegate()->getParent()->getProperties()->setTitle('EK - A Denetim_Ekibi_Bilgisi');
                $event->sheet->getDelegate()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


                $event->sheet->getDelegate()->mergeCells('A1:E1');
                $event->sheet->getDelegate()->mergeCells('B2:E2');
                //$event->sheet->getDelegate()->mergeCells('B3:E3');

                $event->sheet->getDelegate()->getStyle('A1')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A2')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A3')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('C3')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A4:F4')->getFont()->setBold(true);
                

                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(30);

                for ($rowNumber = 1; $rowNumber <= 3; $rowNumber++) {
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

                $highestRow = $event->sheet->getHighestRow();

                for ($rowNumber = 4; $rowNumber <= $highestRow; $rowNumber++) {
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
