<?php

namespace App\Exports;

use App\Models\AwardHistory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AwardHistoryExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'STT',
            'Tên người tham gia',
            'Số điện thoại',
            'Tỉnh/Thành phố',
            'Quận/Huyện',
            'Phường/Xã',
            'Địa chỉ',
            'Nông dân',
            'Giống lúa',
            'Giai đoạn lúa',
            'Sản phẩm đã dùng',
            'Vòng quay',
            'Giải thưởng',
            'Thời gian quay',
            'Trúng thưởng'
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        static $index = 0;
        $index++;

        return [
            $index, // Sequential number instead of ID
            $row->participant->name,
            $row->participant->phone,
            $row->participant->province,
            $row->participant->district,
            $row->participant->ward,
            $row->participant->address,
            $row->participant->is_farmer ? 'Có' : 'Không',
            $row->participant->rice_variety,
            $row->participant->rice_stage,
            $row->participant->used_products,
            $row->luckyWheel->name,
            $row->prize->name ?? 'Không trúng thưởng',
            $row->spin_time->format('d/m/Y H:i:s'),
            $row->is_win ? 'Có' : 'Không'
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return void
     */
    public function styles(Worksheet $sheet)
    {
        // Bold and center alignment for headers
        $sheet->getStyle('A1:O1')->getFont()->setBold(true);
        $sheet->getStyle('A1:O1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Auto-size columns
        foreach(range('A', 'O') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Get the last row with data
                $lastRow = $sheet->getHighestRow();

                // Set the filter range (from the header row to the last data row)
                $filterRange = 'A1:O' . $lastRow;
                $sheet->setAutoFilter($filterRange);

                // Freeze the header row
                $sheet->freezePane('A2');

                // Set alternating row colors for better readability
                for ($row = 2; $row <= $lastRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle('A' . $row . ':O' . $row)
                            ->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setARGB('F9F9F9');
                    }
                }
            },
        ];
    }
}
