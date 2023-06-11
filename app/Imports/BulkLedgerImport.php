<?php


namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use App\Models\Financialtran;
use App\Models\Financialtrandetail;
use App\Models\CommonFeeCollection;
use App\Models\CommonFeeCollectionHeadwise;
use App\Models\Branch;
use App\Models\FeeCategory;
use App\Models\FeeCollectionType;
use App\Models\FeeType;
use App\Models\EntryMode;
use App\Models\User;
use App\Models\TemporaryTable;
use DB;


class BulkLedgerImport implements ToModel, WithHeadingRow, WithBatchInserts
{
    //ini_set('memory_limit', -1);
    private $chunkSize = 1000; // Set the desired chunk size

    public function model(array $row)
    {
        $row = collect($row);
        //dd($row);

        // Finding or Creating Branch            
        $br = DB::table('branches')->where('name', '=', $row['faculty'])->get();
        if($br->value('id')) {
            $br_id= $br->value('id');
        } else {
            $id = Branch::updateOrCreate(['name' => $row['faculty']])->id;
            Feecollectiontype::Insert([
                ['collectionhead' => 'Academic', 'collectiondesc' => 'Academic' ,'br_id' => $id],
                ['collectionhead' => 'Academic Misc', 'collectiondesc' => 'Academic Misc' ,'br_id' => $id],
                ['collectionhead' => 'Hostel', 'collectiondesc' => 'Hostel' ,'br_id' => $id],
                ['collectionhead' => 'Hostel Misc', 'collectiondesc' => 'Hostel Misc' ,'br_id' => $id],
                ['collectionhead' => 'Transport', 'collectiondesc' => 'Transport' ,'br_id' => $id],
                ['collectionhead' => 'Transport Misc', 'collectiondesc' => 'Transport Misc' ,'br_id' => $id]
            ]);
            $br_id = $id;
        }
        
        // Finding or Creating Fee Category            
        $fc = DB::table('feecategory')->where('brid', '=', $br_id)->where('name', '=', $row['fee_category'])->get();
        if ($fc->value('id')){
            $fee_category_id = $fc->value('id');
        } else { 
            $fee_category_id = FeeCategory::create(['name' => $row['fee_category'],'brid' => $br_id])->value('id');
        }

        //FInding Collection ID
        $fee_collection_id = DB::table('feecollectiontypes')->where('br_id', '=',$br_id)->where('collectionhead', '=', 'Academic')->get()->value('id');


        // FIndind the existing FeeTYpe 
        $ft = DB::table('feetypes')->where('br_id', '=',$br_id)->where('fee_category', '=',$fee_category_id)->where('collection_id', '=', $fee_collection_id)->get();
        //dd($fee_category_id,$fee_collection_id,$ft);
        if($ft->value('id')){
            $fee_type_id = $ft->value('id');
        } else {
        //Creating new feetype
        $sq = DB::table('feetypes')->where('br_id', '=',$br_id)->where('fee_category', '=',$fee_category_id)->where('collection_id', '=', $fee_collection_id)->count();
        $sq = ($sq)? intval($sq) : 0 ;
         $feeType = FeeType::create([
             'fee_category' => $fee_category_id,
             'f_name' => $row['fee_head'],
             'collection_id' => $fee_collection_id,
             'br_id' => $br_id,
             'seq_id' => strval($sq + 1),
             'fee_type_ledger' => $row['fee_head'],
             'fee_head_type' => 1,
         ]);
         $fee_type_id = $feeType ->value('id');
        }

        // Cheching Entry Mode Number
        $entry_mode = DB::table('entrymode')->where('entry_modename', '=',$row['voucher_type'])->get();
        $entry_mode_no= ($entry_mode->value('entrymodeno') == 0) ? 0 : $entry_mode->value('entrymodeno');



        //dd('all done!',$feeType);
        // Find Transaction and Update the Amount  
        $financial_txn_id = null;
        $fin_txn = DB::table('financialtrans')->where('admno', '=',$row['admnouniqueid']);
        if($fin_txn->value('amount')){
            $fin_txn->update(['amount' => $fin_txn->get()->value('amount') + $row['paid_amount']]);
            $financial_txn_id = $fin_txn->value('tranid');
        } else {        
            // Creating Financial Transaction
            $type_of_consession = null;
            if($row['voucher_type'] == 'SCHOLARSHIP'){
                $type_of_consession = 1;
            }
            elseif($row['voucher_type'] == 'CONCESSION'){
                $type_of_consession = 2;
            }

            $flag=true;
            while($flag){
                $r = rand(100000, 999999);
                $flag = (Financialtran::where('tranid','=',$r)->count()) ? true : false;
            }
            $financialTransaction = Financialtran::create([
                'tranid' => $r,
                'moduleid' => 1,
                'admno' => $row['admnouniqueid'],
                'amount' => $row['paid_amount'],
                'crdr' => $entry_mode->value('crdr'),
                'tranDate' => $row['date'],
                'acadYear' => $row['academic_year'],
                'entrymode' => $entry_mode_no,
                'voucherno' => $row['voucher_no'],
                'brid' => $br_id,
                'Type_of_concession' => $type_of_consession,
            ]);
            $financial_txn_id = $r;
        }


        // Import Financial Transaction Details
        $financialTransactionDetails = Financialtrandetail::create([
            'financialTranId' => $financial_txn_id,
            'moduleId' => 1,
            'amount' => $row['paid_amount'],
            'headId' => $fee_type_id,
            'crdr' => $entry_mode->value('crdr'),
            'brid' => $br_id,
            'head_name' => $row['fee_head'],
        ]);

        
        
        // Import Common Fee Collection

        $reciept_id = null;
        $cfc = DB::table('commonfeecollection')->where('admno', '=',$row['admnouniqueid']);
        if($cfc->value('amount')){
            $cfc->update(['amount' => $cfc->get()->value('amount') + $row['paid_amount']]);
            $reciept_id = $cfc->value('id');
        } else {

            if(in_array($row['voucher_type'], array('RCPT','JV','PMT')) ){
                $inactive=0;
            }
            elseif(in_array($row['voucher_type'], array('REVRCPT','REVJV','REVPMT')) ){
                $inactive=1;
            }else{
                $inactive=null;
            }
            $commonFeeCollection = CommonFeeCollection::create([
                'moduleId' => 1,
                'transId' => $financial_txn_id,
                'admno' => $row['admnouniqueid'],
                'rollno' => $row['roll_no'],
                'brid' => $br_id,
                'amount' => $row['paid_amount'],
                'acadamicYear' => $row['academic_year'],
                'financialYear' => $row['academic_year'],
                'displayReceiptNo' => $row['receipt_no'],
                'entrymode' => $entry_mode_no,
                'paid_date' => $row['date'],
                'inactive' => $inactive,
            ]);
            $reciept_id = $commonFeeCollection->value('id');
        }

        // Import Common Fee Collection Headwise



        return new CommonFeeCollectionHeadwise([
            'moduleId' => 1,
            'receiptId' => $reciept_id,
            'headId' => $fee_type_id,
            'headName' => $row['fee_head'],
            'brid' => $br_id,
            'amount' => $row['paid_amount'],
        ]);
        //new Entrymode( ['entry_modename'=> '1',	'crdr'=>'2','entrymodeno'=>'3']); // Return null to skip inserting the row into the database
    }

    public function headingRow(): int
    {
        return 6; // Set the heading row index to 1
    }
    public function batchSize(): int
    {
        return $this->chunkSize;
    }

    public function chunkSize(): int
    {
        return $this->chunkSize;
    }
}
?>
