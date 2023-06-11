<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\TemporaryTable;

class TemporaryTableImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // dd($row);
        return new TemporaryTable([
            'date' => $row['date'],
            'academic_year' => $row['academic_year'],
            'session' => $row['session'],
            'alloted_category' => $row['alloted_category'],
            'voucher_type' => $row['voucher_type'],
            'voucher_no' => $row['voucher_no'],
            'roll_no' => $row['roll_no'],
            'admno_uniqueid' => $row['admnouniqueid'],
            'status' => $row['status'],
            'fee_category' => $row['fee_category'],
            'faculty' => $row['faculty'],
            'program' => $row['program'],
            'department' => $row['department'],
            'batch' => $row['batch'],
            'receipt_no' => $row['receipt_no'],
            'fee_head' => $row['fee_head'],
            'due_amount' => $row['due_amount'],
            'paid_amount' => $row['paid_amount'],
            'concession_amount' => $row['concession_amount'],
            'scholarship_amount' => $row['scholarship_amount'],
            'reverse_concession_amount' => $row['reverse_concession_amount'],
            'write_off_amount' => $row['write_off_amount'],
            'adjusted_amount' => $row['adjusted_amount'],
            'refund_amount' => $row['refund_amount'],
            'fund_transfer_amount' => $row['fund_trancfer_amount'],
            'remarks' => $row['remarks'],
        ]);
    }
    public function headingRow(): int
    {
        return 1; // Set the heading row index to 1
    }
}
