<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;

class TemporaryTableSeeder extends Seeder
{
        /**
     * The parameter values to be used in the seeder.
     *
     * @var array
     */
    protected $parameters;

    /**
     * Create a new seeder instance.
     *
     * @param  array  $parameters
     * @return void
     */
    // protected function configure()
    // {
    //     $this->addOption('chunk_id', null, InputOption::VALUE_REQUIRED, 'The ID of the chunk to process');
    // }

    // public function __construct(array $parameters = [])
    // {
    //     $this->parameters = $parameters;
    // }
    /**
     * Run the seeder.
     *
     * @return void
     */
    public function run()
    {
        echo "Started";
        // $chunk_id = $this->parameters['chunk_id'] ?? null;
        // $chunk_id = $this->option('chunk_id');
        $chunk_id = session('chunk_id');
        $fileName= public_path('uploads/chunks/chunk_'.$chunk_id.'.csv');
        // dd($fileName);
        LazyCollection::make(function () {
            $handle = fopen(public_path('uploads\chunks\chunk_'.session('chunk_id').'.csv'), 'r');

            // Skip the first 5 rows
            for ($i = 0; $i < 1; $i++) {
                fgetcsv($handle);
            }

            while (($line = fgetcsv($handle, 4096, ',')) !== false) {
                yield $line;
            }

            fclose($handle);
        })
            ->chunk(100)
            ->each(function ($chunk) {
                $records = [];

                foreach ($chunk as $row) {
                    $records[] = [
                        'id' => intval($row[0]),
                        'date' => $row[1],
                        'academic_year' => $row[2],
                        'session' => $row[3],
                        'alloted_category' => $row[4],
                        'voucher_type' => $row[5],
                        'voucher_no' => intval($row[6]),
                        'roll_no' => $row[7],
                        'admno_uniqueid' => $row[8],
                        'status' => $row[9],
                        'fee_category' => $row[10],
                        'faculty' => $row[11],
                        'program' => $row[12],
                        'department' => $row[13],
                        'batch' => $row[14],
                        'receipt_no' => $row[15],
                        'fee_head' => $row[16],
                        'due_amount' => floatval($row[17]),
                        'paid_amount' => floatval($row[18]),
                        'concession_amount' => floatval($row[19]),
                        'scholarship_amount' => floatval($row[20]),
                        'reverse_concession_amount' => floatval($row[21]),
                        'write_off_amount' => floatval($row[22]),
                        'adjusted_amount' => floatval($row[23]),
                        'refund_amount' => floatval($row[24]),
                        'fund_transfer_amount' => floatval($row[25]),
                        'remarks' => $row[26],
                    ];
                }
                DB::table('temporary_tables')->insertOrIgnore($records);
                // dd($records);
            });
            echo "Completed";
    }
}
