<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use App\Models\Financialtran;
use App\Models\Financialtrandetail;
use App\Models\CommonFeeCollection;
use App\Models\CommonFeeCollectionHeadwise;
use App\Models\Branch;
use App\Models\FeeCategory;
use App\Models\FeeCollectionType;
use App\Models\FeeType;
use App\Models\EntryMode;

class BulkLedgerImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $rows = $rows->skip(4); // Skip the first four rows

        foreach ($rows as $row) {
            // Import Financial Transaction
            $financialTransaction = Financialtran::create([
                'admno' => $row['admno'],
                'amount' => $row['amount'],
                'tranDate' => $row['tranDate'],
                'acadYear' => $row['acadYear'],
                // Add more fields as needed
            ]);

            // Import Financial Transaction Details
            Financialtrandetail::create([
                'financialTranId' => $financialTransaction->id,
                'moduleId' => $row['moduleId'],
                'amount' => $row['amount'],
                'headId' => $row['headId'],
                'crdr' => $row['crdr'],
                'brid' => $row['brid'],
                'head_name' => $row['head_name'],
                // Add more fields as needed
            ]);

            // Import Common Fee Collection
            CommonFeeCollection::create([
                'moduleId' => $row['moduleId'],
                'receiptId' => $row['receiptId'],
                'headId' => $row['headId'],
                'headName' => $row['headName'],
                'brid' => $row['brid'],
                'amount' => $row['amount'],
                // Add more fields as needed
            ]);

            // Import Common Fee Collection Headwise
            CommonFeeCollectionHeadwise::create([
                'moduleId' => $row['moduleId'],
                'receiptId' => $row['receiptId'],
                'headId' => $row['headId'],
                'headName' => $row['headName'],
                'brid' => $row['brid'],
                'amount' => $row['amount'],
                // Add more fields as needed
            ]);

            // Import Branch
            Branch::create([
                'name' => $row['branch_name'],
                // Add more fields as needed
            ]);

            // Import Fee Category
            FeeCategory::create([
                'name' => $row['fee_category'],
                // Add more fields as needed
            ]);

            // Import Fee Collection Type
            FeeCollectionType::create([
                'name' => $row['fee_collection_type'],
                // Add more fields as needed
            ]);

            // Import Fee Type
            FeeType::create([
                'name' => $row['fee_type'],
                // Add more fields as needed
            ]);

            // Import Entry Mode
            EntryMode::create([
                'name' => $row['entry_mode'],
                // Add more fields as needed
            ]);

            // Add logic to handle other imports as per your requirement
        }
    }
}
