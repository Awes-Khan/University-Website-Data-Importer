<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BulkLedgerImport;

class ExcelImportController extends Controller
{
    public function import(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'excel_file' => 'required|mimes:csv',
        ]);

        // Get the uploaded file
        $file = $request->file('excel_file');

        // Import the data from the CSV file
        try {
            Excel::import(new BulkLedgerImport, $file);

            // Return a response or redirect back with success message
            return redirect()->back()->with('success', 'CSV file imported successfully.');
        } catch (\Exception $e) {
            // Handle the exception and show an error message
            return redirect()->back()->with('error', 'Error importing CSV file: ' . $e->getMessage());
        }
    }
}
