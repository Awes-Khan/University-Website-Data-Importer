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

class BulkLedgerImportOld implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $rows = $rows->skip(4); // HAve to Skip the first four rows

        foreach ($rows as $row) {

            print_r($row);
            echo '\n';

            // Finding or CReating Branch
            //$br_id= (Branch::where('name' , $row['Department'])) ? Branch::updateOrCreate(['name' => $row['Department']])->id : Branch::where('name', $row['Department'])->id;
            // Import Financial Transaction
            
            // $financialTransaction = Financialtran::create([
            //     'tranid' => rand(100000,999999),
            //     'moduleid' => $row['Module ID'],
            //     'admno' => $row['Admno/UniqueId'],
            //     'amount' => $row['Paid Amount'],
            //     'crdr' => $row['Status'],
            //     'tranDate' => $row['Date'],
            //     'acadYear' => $row['Academic Year'],
            //     'entrymode' => $row['Entry Mode'],
            //     'voucherno' => $row['Voucher No.'],
            //     'brid' => $br_id,
            //     'Type_of_concession' => $row['Type of concession'],
            // ]);
            // // Import Financial Transaction Details
            // $financialTransactionDetails = Financialtrandetail::create([
            //     'financialTranId' => $financialTransaction->id,
            //     'moduleId' => $row['Module ID'],
            //     'amount' => $row['Paid Amount'],
            //     'headId' => $row['Head ID'],
            //     'crdr' => $row['Status'],
            //     'brid' => $br_id,
            //     'head_name' => $row['Fee Head'],
            // ]);

            // // Import Common Fee Collection
            // $commonFeeCollection = CommonFeeCollection::create([
            //     'moduleId' => $row['Module ID'],
            //     'transId' => $row['Transaction ID'],
            //     'admno' => $row['Admno/UniqueId'],
            //     'rollno' => $row['Roll No.'],
            //     'brid' => $br_id,
            //     'amount' => $row['Paid Amount'],
            //     'acadamicYear' => $row['Academic Year'],
            //     'financialYear' => $row['Financial Year'],
            //     'displayReceiptNo' => $row['Receipt No.'],
            //     'entrymode' => $row['Entry Mode'],
            //     'paid_date' => $row['Paid Date'],
            //     'inactive' => $row['Inactive'],
            // ]);
            // // Import Common Fee Collection Headwise
            // $commonFeeCollectionHeadwise = CommonFeeCollectionHeadwise::create([
            //     'moduleId' => $row['Module ID'],
            //     'receiptId' => $commonFeeCollection->id,
            //     'headId' => $row['Head ID'],
            //     'headName' => $row['Head Name'],
            //     'brid' => $br_id,
            //     'amount' => $row['Amount'],
            // ]);



            // // Import Fee Category
            // $feeCategory = FeeCategory::create([
            //     'name' => $row['Name'],
            //     'brid' => $br_id,
            // ]);

            // // Import Fee Collection Types
            // $feeCollectionType = FeeCollectionType::create([
            //     'collectionhead' => $row['Collection Head'],
            //     'collectiondesc' => $row['Collection Description'],
            //     'br_id' => $br_id,
            // ]);

            // // Import Fee Types
            // $feeType = FeeType::create([
            //     'fee_category' => $row['Fee Category'],
            //     'f_name' => $row['Fee Name'],
            //     'collection_id' => $row['Collection ID'],
            //     'br_id' => $br_id,
            //     'seq_id' => $row['Sequence ID'],
            //     'fee_type_ledger' => $row['Fee Type Ledger'],
            //     'fee_head_type' => $row['Fee Head Type'],
            // ]);
            
            // Import Entry Modes
            // $entryMode = EntryMode::create([
            //     'entry_modename' => $row['Entry Mode Name'],
            //     'crdr' => $row['CR/DR'],
            //     'entrymodeno' => $row['Entry Mode No'],
            // ]);
            
            // Import Modules
            // $module = Module::create([
            //     'module' => $row['Module'],
            //     'moduleid' => $row['Module ID'],
            // ]);

        }
    }

    public function headingRow(): int
    {
        return 5;
    }
}
