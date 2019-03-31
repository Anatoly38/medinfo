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
        $d->batchExport($documents);

        return 'Выгружены';
    }
}
