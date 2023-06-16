<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\Debugbar\Facade as Debugbar;
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

class ExcelImportController extends Controller
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

        $cfc_id = DB::table('commonfeecollectionheadwise')->whereBetween('id', [(($id-1) * 10000)+1, $id * 10000])->orderByDesc('id')->first('id');
        $cfc_id = ($cfc_id)? $cfc_id->id : (($id-1) * 10000);
        $temp_id = DB::table('temporary_tables')->orderByDesc('id')->first('id')->id;
        $total_chunks = intval($temp_id /10000) + 1;
        $start_id = $cfc_id;
        // if($cfc_id >= $temp_id){
        //     return 'Dta Process into all Tables';
        // }
        if($id * 10000 < $cfc_id){ //skipping chunk of 10000
            return redirect()->route('processData',['id'=>($id + 1)]);
        }
        // TemporaryTable::skip($start_id)->take(10000)->chunk(100, function ($rows) {
        TemporaryTable::whereBetween('id', [$start_id + 1, $id * 10000])->chunk(1000, function ($rows) {
            foreach ($rows as $row) {
                // Process each row
                Debugbar::info($row->id);

                echo '<script> console.log("'.$row->id.'"); </script>';
                $this->processRow($row);
            }
        });
        Debugbar::info('Total chunks completed : '.$id);
        // dd('Chunk Done');
        if($id<=$total_chunks){
            return redirect()->route('processData',['id'=>($id + 1)]);
        }
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
            // dd($row['id']);
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



























    public function divideCsvIntoChunks_old()
    {
        $inputCsvFile = public_path('uploads/'. date("Y-m-d") .'/input.csv');

        // Path to the output directory for chunk CSV files
        $outputDirectory = public_path('uploads/'. date("Y-m-d") .'/chunks');

        // Create the output directory if it doesn't exist
        if (!is_dir($outputDirectory)) {
            mkdir($outputDirectory, 0777, true);
        }

        // Create a CSV reader for the input file
        $csv = Reader::createFromPath($inputCsvFile, 'r');
        // Skip the first 5 rows
        $numRowsToSkip = 5;
        for ($i = 0; $i < $numRowsToSkip; $i++) {
            $csv->fetchOne(); // Read and discard the row
        }

        // Set the header offset to 5 since the 6th row is the header
        $csv->setHeaderOffset(5);

        // Set the chunk size
        $chunkSize = 10000;

        // Get the total number of rows in the CSV
        $totalRows = $csv->count();

        // Calculate the number of chunks
        $numChunks = ceil($totalRows / $chunkSize);

        // Read the CSV data into an array
        $data = $csv->getRecords();
        // Iterate over the data and create chunk files
        for ($chunkIndex = 0; $chunkIndex < $numChunks; $chunkIndex++) {
            // Create a CSV writer for the chunk file
            $chunkCsvFile = $outputDirectory . '/chunk_' . ($chunkIndex + 1) . '.csv';
            $chunkCsv = Writer::createFromPath($chunkCsvFile, 'w+');

            // Write the header row to the chunk file
            $header = $csv->getHeader();
            $chunkCsv->insertOne($header);

            // Determine the start and end indexes for the current chunk
            $start = $chunkIndex * $chunkSize;
            $end = min(($chunkIndex + 1) * $chunkSize, $totalRows);

            // Get the data for the current chunk
            $chunkData = array_slice($data, $start, $end - $start);

            // Write the chunk data to the chunk file
            foreach ($chunkData as $row) {
                $chunkCsv->insertOne($row);
            }
            dd($chunkCsv);
            // Close the chunk file
            $chunkCsv->output();
        }

        return 'CSV divided into chunks successfully!';
    }

    public function upload_old(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            // Validate the file
            $validator = Validator::make($request->all(), [
                'file' => 'required|mimes:csv,txt'
            ]);
            
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator);
            }
            
            // Process the file in chunks
            $reader = Reader::createFromPath($file->getRealPath(), 'r');
            $reader->setHeaderOffset(5); // Assuming the first row contains headers

 
            $chunkSize = 1; // Number of rows to process per chunk
            $records = $reader->getRecords();

            $skippedRows=0;
            $row_counter=1;
            echo "started processing";
            foreach ($records as $offset => $record) {
                if ($skippedRows < 5) {
                    $skippedRows++;
                    continue;
                }
                // if (($offset % $chunkSize) === 0) {
                    // Process the chunk and save it to the database
                    // dd($record);
                    $this->processChunk($record);
                    echo "Recorded:".$row_counter." \n";
                    $row_counter++;
                // }
            }
            
            return redirect()->back()->with('success', 'File uploaded successfully.');
        }
        
        return redirect()->back()->with('error', 'Please select a CSV file.');
    }
    
    private function processChunk($record)
    {
        // Assuming your table has columns: column1, column2, column3
        // $model = new TemporaryTable([
        //     'date' => $row['date'],
        //     'academic_year' => $row['academic_year'],
        //     'session' => $row['session'],
        //     'alloted_category' => $row['alloted_category'],
        //     'voucher_type' => $row['voucher_type'],
        //     'voucher_no' => $row['voucher_no'],
        //     'roll_no' => $row['roll_no'],
        //     'admno_uniqueid' => $row['admno_uniqueid'],
        //     'status' => $row['status'],
        //     'fee_category' => $row['fee_category'],
        //     'faculty' => $row['faculty'],
        //     'program' => $row['program'],
        //     'department' => $row['department'],
        //     'batch' => $row['batch'],
        //     'receipt_no' => $row['receipt_no'],
        //     'fee_head' => $row['fee_head'],
        //     'due_amount' => $row['due_amount'],
        //     'paid_amount' => $row['paid_amount'],
        //     'concession_amount' => $row['concession_amount'],
        //     'scholarship_amount' => $row['scholarship_amount'],
        //     'reverse_concession_amount' => $row['reverse_concession_amount'],
        //     'write_off_amount' => $row['write_off_amount'],
        //     'adjusted_amount' => $row['adjusted_amount'],
        //     'refund_amount' => $row['refund_amount'],
        //     'fund_transfer_amount' => $row['fund_trancfer_amount'],
        //     'remarks' => $row['remarks'],
        //]);
        // $model->column1 = $record['column1'];
        // $model->column2 = $record['column2'];
        // $model->column3 = $record['column3'];
        // $model->save();

        $model = new TemporaryTable();
        $model->date = $record['Date'];
        $model->academic_year = $record['Academic Year'];
        $model->session = $record['Session'];
        $model->alloted_category = $record['Alloted Category'];
        $model->voucher_type = $record['Voucher Type'];
        $model->voucher_no = $record['Voucher No.'];
        $model->roll_no = $record['Roll No.'];
        $model->admno_uniqueid = $record['Admno/UniqueId'];
        $model->status = $record['Status'];
        $model->fee_category = $record['Fee Category'];
        $model->faculty = $record['Faculty'];
        $model->program = $record['Program'];
        $model->department = $record['Department'];
        $model->batch = $record['Batch'];
        $model->receipt_no = $record['Receipt No.'];
        $model->fee_head = $record['Fee Head'];
        $model->due_amount = $record['Due Amount'];
        $model->paid_amount = $record['Paid Amount'];
        $model->concession_amount = $record['Concession Amount'];
        $model->scholarship_amount = $record['Scholarship Amount'];
        $model->reverse_concession_amount = $record['Reverse Concession Amount'];
        $model->write_off_amount = $record['Write Off Amount'];
        $model->adjusted_amount = $record['Adjusted Amount'];
        $model->refund_amount = $record['Refund Amount'];
        $model->fund_transfer_amount = $record['Fund TranCfer Amount'];
        $model->remarks = $record['Remarks'];
        $model->save();
    }
    public function import(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:csv',
        ]);

        // Get the uploaded file
        $file = $request->file('file');
        // Import the data from the CSV file
        try {
            //Excel::import(new BulkLedgerImport, $file);
            dd(Excel::import(new TemporaryTableImport, $file));
            //Excel::filter('chunk')->chunkSize(1000)->import(new BulkLedgerImport, $file);

            // Return a response or redirect back with success message
            return redirect()->back()->with('success', 'CSV file imported successfully.');
        } catch (\Exception $e) {
            // Handle the exception and show an error message
            return redirect()->back()->with('error', 'Error importing CSV file: ' . $e->getMessage());
        }
    }
    public function import_old(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:csv',
        ]);

        // Get the uploaded file
        $file = $request->file('file');
        // Import the data from the CSV file
        try {
            //Excel::import(new BulkLedgerImport, $file);
            dd(Excel::import(new BulkLedgerImport, $file));
            //Excel::filter('chunk')->chunkSize(1000)->import(new BulkLedgerImport, $file);

            // Return a response or redirect back with success message
            return redirect()->back()->with('success', 'CSV file imported successfully.');
        } catch (\Exception $e) {
            // Handle the exception and show an error message
            return redirect()->back()->with('error', 'Error importing CSV file: ' . $e->getMessage());
        }
    }
}
