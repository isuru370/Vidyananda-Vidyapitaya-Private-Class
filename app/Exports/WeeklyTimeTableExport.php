<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WeeklyTimeTableExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $schedules;
    protected $startOfWeek;
    protected $endOfWeek;

    public function __construct($schedules, $startOfWeek, $endOfWeek)
    {
        $this->schedules = $schedules;
        $this->startOfWeek = $startOfWeek;
        $this->endOfWeek = $endOfWeek;
    }

    public function collection()
    {
        return $this->schedules;
    }

    public function headings(): array
    {
        return [
            'No.',
            'Date',
            'Day',
            'Start Time',
            'End Time',
            'Hall',
            'Class Name',
            'Grade',
            'Category',
            'Fee Options',
            'Status',
        ];
    }

    public function map($schedule): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $statusText = [
            'scheduled' => 'Scheduled',
            'ongoing'   => 'Ongoing',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        $classDate = $schedule->class_date
            ? Carbon::parse($schedule->class_date)
            : null;

        $startTime = $schedule->start_time
            ? Carbon::parse($schedule->start_time)
            : null;

        $endTime = $schedule->end_time
            ? Carbon::parse($schedule->end_time)
            : null;

        /*
         * Get active fee options for this class category fee.
         *
         * Example:
         * Theory Only - Rs. 2500.00
         * Special      - Rs. 2000.00
         */
        $feeOptions = optional($schedule->classCategoryFee)
            ->activeFeeOptions;

        $feeOptionText = '-';

        if ($feeOptions && $feeOptions->count() > 0) {
            $feeOptionText = $feeOptions->map(function ($option) {
                return $option->label . ' - Rs. ' . number_format(
                    (float) $option->fee,
                    2
                );
            })->implode(', ');
        }

        return [
            $rowNumber,

            $classDate
                ? $classDate->format('Y-m-d')
                : '-',

            $classDate
                ? $classDate->format('l')
                : '-',

            $startTime
                ? $startTime->format('h:i A')
                : '-',

            $endTime
                ? $endTime->format('h:i A')
                : '-',

            optional($schedule->hall)->hall_name
                ?: 'N/A',

            optional($schedule->studentClass)->class_name
                ?: 'N/A',

            optional(optional($schedule->studentClass)->grade)->grade_name
                ?: 'N/A',

            optional(optional($schedule->classCategoryFee)->category)->category_name
                ?: 'N/A',

            $feeOptionText,

            $statusText[$schedule->status]
                ?? ucfirst((string) $schedule->status),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
            ],

            'A1:K1' => [
                'font' => [
                    'bold' => true,
                ],
            ],
        ];
    }
}