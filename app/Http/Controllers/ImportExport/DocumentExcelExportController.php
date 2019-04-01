<?php

namespace App\Http\Controllers\ImportExport;


use App\Medinfo\ImportExport\DocumentExcelExport;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class DocumentExcelExportController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('admins');
    }

    public function exportDocuments(Request $request)
    {
        $documents = explode(",", $request->documents );
        $d = new DocumentExcelExport();
        try {
            $zip_archiv = $d->batchExport($documents);
            $headers =  ['Content-Type' => 'application/zip'];
            return response()->download($zip_archiv, basename($zip_archiv), $headers)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
    }
}
