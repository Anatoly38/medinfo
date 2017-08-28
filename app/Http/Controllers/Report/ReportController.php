<?php

namespace App\Http\Controllers\Report;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Form;
use App\Period;
use App\UnitsView;

class ReportController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('analytics');
    }

    public function compose_query()
    {
        $forms = Form::orderBy('form_index')->get(['id', 'form_code']);
        $periods = Period::orderBy('name')->get();
        $last_year = Period::LastYear()->first();
        $upper_levels = UnitsView::whereIn('type', [1,2,5])->get();
        return view('reports.composequickquery', compact('forms', 'upper_levels', 'periods', 'last_year'));
    }


}
