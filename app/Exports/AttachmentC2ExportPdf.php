<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;


class AttachmentC2ExportPdf implements FromArray, WithEvents
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
                $event->sheet->getDelegate()->getParent()->getProperties()->setTitle('EK-C.2: VARLIK GRUBU VE KRİTİKLİK DERECESİ TANIMLAMA FORMU');

                $event->sheet->getDelegate()->mergeCells('A1:M1');
                $event->sheet->getDelegate()->mergeCells('A2:A3');
                $event->sheet->getDelegate()->mergeCells('B2:B3');
                $event->sheet->getDelegate()->mergeCells('C2:C3');
                $event->sheet->getDelegate()->mergeCells('M2:M3');
                $event->sheet->getDelegate()->mergeCells('D2:I2');
                $event->sheet->getDelegate()->mergeCells('J2:L2');



              

                $event->sheet->getDelegate()->getStyle('B2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $event->sheet->getDelegate()->getStyle('C2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $event->sheet->getDelegate()->getStyle('M2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);




                $event->sheet->getDelegate()->getStyle('A1')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A2')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('M2')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('B2')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('C2')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A3:M3')->getFont()->setBold(true);

                $event->sheet->getDelegate()->getStyle('B1')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('D2')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('J2')->getFont()->setBold(true);

                $event->sheet->getDelegate()->getStyle('A12')->getFont()->setBold(true);
                
                $event->sheet->getDelegate()->getStyle('A:M')->getAlignment()->setWrapText(true);
                // $event->sheet->getDelegate()->getStyle('D2')->getAlignment()->setWrapText(true);
                // $event->sheet->getDelegate()->getStyle('J2')->getAlignment()->setWrapText(true);

                $event->sheet->getDelegate()->getRowDimension(2)->setRowHeight(40);


                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(5);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('L')->setWidth(15);
                $event->sheet->getDelegate()->getColumnDimension('M')->setWidth(10);



               

                $highestRow = $event->sheet->getHighestRow();

                for ($rowNumber = 1; $rowNumber <= $highestRow; $rowNumber++) {
                    $cellRange = 'A'.$rowNumber.':'.'M'.$rowNumber; // Dinamik olarak hücre aralığını belirle
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
