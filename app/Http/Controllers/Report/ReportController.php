<?php

namespace App\Http\Controllers\Report;

use App\Medinfo\ReportMaker;
use App\PeriodPattern;
use App\ReportPattern;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('analytics');
    }

    public function compose_query()
    {
        $forms = \App\Form::orderBy('form_index')->get(['id', 'form_code']);
        $periods = \App\Period::orderBy('name')->get();
        $last_year = \App\Period::LastYear()->first();
        $upper_levels = \App\UnitsView::whereIn('type', ['1','2','100'])->get();
        return view('reports.composequickquery', compact('forms', 'upper_levels', 'periods', 'last_year'));
    }

    public function selectAnalitycReportToPerform()
    {
        $patterns = \App\ReportPattern::orderBy('name')->get(['id', 'name']);
        $periods = \App\Period::orderBy('name')->get();
        $last_year = \App\Period::LastYear()->first();
        //dd($patterns);
        return view('reports.analytic_report_by_patterns', compact('patterns', 'periods', 'last_year'));
    }

    public function performReport(Request $request)
    {
        $pattern = ReportPattern::find($request->pattern);
        $period = $request->period;
        $extrafields = explode(',', $request->show);
        $structure = json_decode($pattern->pattern, true);
        //dd($structure);
        $count_of_indexes = count($structure['content']);
        $title = $structure['header']['title'];
        //$indexes = ReportMaker::makeReportByLegal($structure, $level, $period);
        $rp = new ReportMaker($request->group_by, $period, $request->group_by);
        $result = $rp->makeReportByLegal($structure);
        $indexes = $result[0];
        $calculation_errors = $result[1];
        return view('reports.report', compact('indexes', 'title', 'structure', 'count_of_indexes', 'calculation_errors', 'extrafields'));
    }

    public function getProgess() {
        $id = \Session::getId();
        $manadged = \Session::get('report_progress');
        $current_unit = trim(\Session::get('current_unit'));
        $all = \Session::get('count_of_units');
        $progress = round($manadged/$all*100, 1);
        return ['session_id' => $id, 'manadged' => $manadged, 'current_unit' => $current_unit, 'count_of_units' => $all, 'progress' => $progress ] ;
    }

}
