<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;


class GapAnlysisExportPdf implements FromArray, WithEvents
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
                $event->sheet->getDelegate()->getParent()->getProperties()->setTitle('Boşluk Analizi Raporu');

                $event->sheet->getDelegate()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                //$event->sheet->getDelegate()->getStyle('A10')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $event->sheet->getDelegate()->mergeCells('A1:I1');
                // $event->sheet->getDelegate()->mergeCells('A2:E2');
                // $event->sheet->getDelegate()->mergeCells('A6:E6');
                // $event->sheet->getDelegate()->mergeCells('B4:E4');
                // $event->sheet->getDelegate()->mergeCells('D5:E5');
                // $event->sheet->getDelegate()->mergeCells('A10:E10');
                // $event->sheet->getDelegate()->mergeCells('A14:E14');


                $event->sheet->getDelegate()->getStyle('A1')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A3:I3')->getFont()->setBold(true);
     

                $event->sheet->getDelegate()->getStyle('C')->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle('D')->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle('E')->getAlignment()->setWrapText(true);


                // $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(25);
                 $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(30);
                 $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(100);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(20);



                $highestRow = $event->sheet->getHighestRow();

                // for ($rowNumber = 1; $rowNumber <= 13; $rowNumber++) {
                //     $cellRange = 'A'.$rowNumber.':'.'E'.$rowNumber; // Dinamik olarak hücre aralığını belirle
                //     $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                //         'borders' => [
                //             'allBorders' => [
                //                 'borderStyle' => Border::BORDER_THIN,
                //                 'color' => ['argb' => '000000'],
                //             ],
                //         ]
                //     ]);
                // }

                for ($rowNumber = 1; $rowNumber <= $highestRow ; $rowNumber++) {
                    $cellRange = 'A'.$rowNumber.':'.'I'.$rowNumber; // Dinamik olarak hücre aralığını belirle
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
