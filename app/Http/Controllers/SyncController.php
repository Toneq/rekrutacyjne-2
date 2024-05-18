<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SyncProductsJob;

class SyncController extends Controller
{
    public function sync(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $csvFilePath = $request->file('csv_file')->store('csv_files');

        SyncProductsJob::dispatch(storage_path('app/' . $csvFilePath));

        return response()->json(['message' => 'Sync job dispatched!'], 200);
    }
}