<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BulkLedgerImport;
use App\Imports\TemporaryTableImport;
use App\Models\TemporaryTable;
use League\Csv\Reader;
use League\Csv\Writer;
use Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use App\Models\FeeCategory;
use App\Models\Feecollectiontype;
use App\Models\FeeType;
use App\Models\Financialtran;
use App\Models\Financialtrandetail;
use App\Models\CommonFeeCollection;
use App\Models\CommonFeeCollectionHeadwise;

class ProcessDataController extends Controller
{

    /**
     * Handle the file upload.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');

        // $fileName = 'csv_' . $file->getClientOriginalName();
        $fileName = 'input.csv';
        // Move the file to the public directory
        $file->move(public_path('uploads/'. date("Y-m-d")), $fileName);
        divideCsvIntoChunks();
        runSeeder(1);
        
        return response()->json([
            'message' => 'File uploaded successfully.',
            'path' => '/uploads/'. date("Y-m-d") .''. $fileName,
        ]);
    }

    public function divideCsvIntoChunks()
    {
        $inputCsvFile = public_path('uploads/'. date("Y-m-d") .'/input.csv');

        $outputDirectory = public_path('uploads/'. date("Y-m-d") .'/chunks');

        if (!is_dir($outputDirectory)) {
            mkdir($outputDirectory, 0777, true);
        }

        $inputCsv = fopen($inputCsvFile, 'r');

        // Skip the first 5 rows
        for ($i = 0; $i < 5; $i++) {
            fgetcsv($inputCsv);
        }

        $chunkSize = 1000;

        $chunkIndex = 1;

        // Create the first chunk CSV file
        $chunkCsvFile = $outputDirectory . '/chunk_' . $chunkIndex . '.csv';
        $chunkCsv = fopen($chunkCsvFile, 'w');

        // Write the header row to the chunk file
        $header = fgetcsv($inputCsv);
        fputcsv($chunkCsv, $header);

        // Iterate over the remaining rows and create chunk files
        while (($row = fgetcsv($inputCsv)) !== false) {
            fputcsv($chunkCsv, $row);

            if (ftell($chunkCsv) >= $chunkSize) {
                fclose($chunkCsv);

                $chunkIndex++;

                $chunkCsvFile = $outputDirectory . '/chunk_' . $chunkIndex . '.csv';
                $chunkCsv = fopen($chunkCsvFile, 'w');

                fputcsv($chunkCsv, $header);
            }
        }

        fclose($chunkCsv);

        fclose($inputCsv);

        session(['chunk_count' => count( File::files($outputDirectory))]);

        return 'CSV divided into chunks successfully!';
    }


    public function runSeeder($id)
    {
        // Run the seeder class
        session(['chunk_id' => $id]);
        Artisan::call('db:seed', [
            '--class' => 'TemporaryTableSeeder',
            // '--chunk_id' => $id,

        ]);

        $output = Artisan::output();
        print_r($output);

        $nextId = $id + 1;

        if($nextId >= session('chunk_count')){
            return redirect()->route('import');
        }
        return redirect()->route('runSeeder', ['id' => $nextId]);
    }


    public function processData($id)
    {

        $cfc_id = DB::table('commonfeecollectionheadwise')->orderByDesc('id')->first();
        $temp_id = DB::table('temporary_tables')->orderByDesc('id')->first();
        $chunk_count = ($temp_id/10000);
        $start_id = $cfc_id;
        if($cfc_id == $temp_id){
            return 'Dta Process into all Tables';
        }
        if($id * 10000 < $cfc_id){
            return redirect()->route('processData',['id'=>($id + 1)]);
        }
        TemporaryTable::skip($start_id)->take(10000)->chunk(1000, function ($rows) {
            foreach ($rows as $row) {
                // Process each row
                $this->processRow($row);
            }
        });
        return 'Dta Process into all Tables';
    }

    public function ProcessRow($row)
        {
    
            $br = DB::table('branches')->where('name', '=', $row['faculty'])->first();
            if ($br) {
                $br_id = $br->id;
            } else {
                $branch = Branch::create(['name' => $row['faculty']]);
                $id = $branch->id;
                // Insert default fee collection types along withh new branch
                Feecollectiontype::insert([
                    ['collectionhead' => 'Academic', 'collectiondesc' => 'Academic' ,'br_id' => $id],
                    ['collectionhead' => 'Academic Misc', 'collectiondesc' => 'Academic Misc' ,'br_id' => $id],
                    ['collectionhead' => 'Hostel', 'collectiondesc' => 'Hostel' ,'br_id' => $id],
                    ['collectionhead' => 'Hostel Misc', 'collectiondesc' => 'Hostel Misc' ,'br_id' => $id],
                    ['collectionhead' => 'Transport', 'collectiondesc' => 'Transport' ,'br_id' => $id],
                    ['collectionhead' => 'Transport Misc', 'collectiondesc' => 'Transport Misc' ,'br_id' => $id]
                ]);
                $br_id = $id;
            }
            
            $module_id = 1;

            // Finding or Creating Fee Category            
            $fc = DB::table('feecategory')->where('brid', '=', $br_id)->where('name', '=', $row['fee_category'])->first();
            if ($fc){
                $fee_category_id = $fc->id;
            } else { 
                $fee_category = FeeCategory::create(['name' => $row['fee_category'],'brid' => $br_id]);
                $fee_category_id = $fee_category->id;
            }
    
            // Finding Collection ID
            $fee_collection_id = DB::table('feecollectiontypes')->where('br_id', '=', $br_id)->where('collectionhead', '=', 'Academic')->value('id');
    
            // Finding the existing Fee Type 
            $ft = DB::table('feetypes')->where('br_id', '=', $br_id)->where('fee_category', '=', $fee_category_id)->where('collection_id', '=', $fee_collection_id)->first();
            if ($ft){
                $fee_type_id = $ft->id;
            } else {
                // Creating new fee type
                $sq = DB::table('feetypes')->where('br_id', '=', $br_id)->where('fee_category', '=', $fee_category_id)->where('collection_id', '=', $fee_collection_id)->count();
                $sq = ($sq) ? intval($sq) : 0 ;
                $feeType = FeeType::create([
                    'fee_category' => $fee_category_id,
                    'f_name' => $row['fee_head'],
                    'collection_id' => $fee_collection_id,
                    'br_id' => $br_id,
                    'seq_id' => strval($sq + 1),
                    'fee_type_ledger' => $row['fee_head'],
                    'fee_head_type' => 1,
                ]);
                $fee_type_id = $feeType->id;
            }
    
            // Checking Entry Mode Number
            $entry_mode = DB::table('entrymode')->where('entry_modename', '=', $row['voucher_type'])->first();
            $entry_mode_no = ($entry_mode && $entry_mode->entrymodeno != 0) ? $entry_mode->entrymodeno : 0;
    
            // Find Transaction and Update the Amount  
            $financial_txn = DB::table('financialtrans')->where('admno', '=', $row['admno_uniqueid'])->first();
            if ($financial_txn) {
                DB::table('financialtrans')->where('admno', '=', $row['admno_uniqueid'])->update(['amount' => $financial_txn->amount + $row['paid_amount']]);
                $financial_txn_id = $financial_txn->tranid;
            } else {
                // Creating Financial Transaction
                $type_of_consession = null;
                if ($row['voucher_type'] == 'SCHOLARSHIP') {
                    $type_of_consession = 1;
                } elseif ($row['voucher_type'] == 'CONCESSION') {
                    $type_of_consession = 2;
                }
    
                $flag = true;
                while ($flag) {
                    $r = rand(100000, 999999);
                    $flag = (Financialtran::where('tranid', '=', $r)->count()) ? true : false;
                }
    
                $financialTransaction = Financialtran::create([
                    'tranid' => $r,
                    'moduleid' => $module_id,
                    'admno' => $row['admno_uniqueid'],
                    'amount' => $row['paid_amount'],
                    'crdr' => $entry_mode->crdr,
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
            $financialTransactionDetails = Financialtrandetail::updateOrCreate([
                'id' => $row['id'],
                'financialTranId' => $financial_txn_id,
                'moduleId' => $module_id,
                'amount' => $row['paid_amount'],
                'headId' => $fee_type_id,
                'crdr' => $entry_mode->crdr,
                'brid' => $br_id,
                'head_name' => $row['fee_head'],
            ]);
    
            // Import Common Fee Collection
            $reciept_id = null;
            $cfc = DB::table('commonfeecollection')->where('admno', '=', $row['admno_uniqueid'])->first();
            if ($cfc) {
                DB::table('commonfeecollection')->where('admno', '=', $row['admno_uniqueid'])->update(['amount' => $cfc->amount + $row['paid_amount']]);
                $reciept_id = $cfc->id;
            } else {
                $inactive = null;
                if (in_array($row['voucher_type'], array('RCPT','JV','PMT'))) {
                    $inactive = 0;
                } elseif (in_array($row['voucher_type'], array('REVRCPT','REVJV','REVPMT'))) {
                    $inactive = 1;
                }
    
                $commonFeeCollection = CommonFeeCollection::create([
                    'moduleId' => $module_id,
                    'transId' => $financial_txn_id,
                    'admno' => $row['admno_uniqueid'],
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
                $reciept_id = $commonFeeCollection->id;
            }
    
            // Import Common Fee Collection Headwise
            CommonFeeCollectionHeadwise::updateOrCreate([
                'id' => $row['id'],
                'moduleId' => $module_id,
                'receiptId' => $reciept_id,
                'headId' => $fee_type_id,
                'headName' => $row['fee_head'],
                'brid' => $br_id,
                'amount' => $row['paid_amount'],
            ]);
            return ' Data Processed';
        }
    
    }