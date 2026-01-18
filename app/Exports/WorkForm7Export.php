<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;

class WorkForm7Export implements FromArray, WithEvents
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
                
                $event->sheet->getDelegate()->mergeCells('A1:D1');
                $event->sheet->getDelegate()->mergeCells('B2:D2');
                $event->sheet->getDelegate()->mergeCells('A3:D3');

                $event->sheet->getDelegate()->mergeCells('A7:D7');
                $event->sheet->getDelegate()->mergeCells('B8:D8');
                $event->sheet->getDelegate()->mergeCells('A9:D9');
                $event->sheet->getDelegate()->mergeCells('A10:D10');
                $event->sheet->getDelegate()->mergeCells('A13:D13');


                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(30);

                $event->sheet->getDelegate()->getStyle('A1')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A2')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A4')->getFont()->setBold(true); 
                $event->sheet->getDelegate()->getStyle('A5')->getFont()->setBold(true); 
                $event->sheet->getDelegate()->getStyle('A6')->getFont()->setBold(true); 
                $event->sheet->getDelegate()->getStyle('A8')->getFont()->setBold(true); 
                $event->sheet->getDelegate()->getStyle('A10')->getFont()->setBold(true); 
               
                $event->sheet->getDelegate()->getStyle('A11:D11')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A14:D14')->getFont()->setBold(true); 



                $event->sheet->getDelegate()->getStyle('C4')->getFont()->setBold(true); 
                $event->sheet->getDelegate()->getStyle('C5')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('C6')->getFont()->setBold(true); 
                $event->sheet->getDelegate()->getStyle('B1')->getFont()->setBold(true); 


                $event->sheet->getDelegate()->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                
                $event->sheet->getDelegate()->getStyle('B')->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle('D')->getAlignment()->setWrapText(true);


                $highestRow = $event->sheet->getHighestRow();

                for ($rowNumber = 1; $rowNumber <= $highestRow; $rowNumber++) {
                    $cellRange = 'A'.$rowNumber.':'.'D'.$rowNumber; // Dinamik olarak hücre aralığını belirle
                    $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ]
                    ]);
                }
                

            },
        ];
    }

}
