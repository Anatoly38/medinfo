<?php

namespace App\Http\Controllers\Admin;

use Carbon\CarbonInterval;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Period;
use App\PeriodPattern;
use App\Document;
use Carbon\Carbon;

class PeriodAdminController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('admins');
    }

    public function index()
    {
        $period_patterns = PeriodPattern::orderBy('id')->get();
        //dd($period_patterns);
        $years = $this->getYearsArray();
        return view('jqxadmin.periods', compact('period_patterns', 'years'));
    }

    public function getYearsArray()
    {
        $upto = 2030;
        $years = [];
        for ($initial = 2013 ; $upto >= $initial; $initial++) {
            $years[] = $initial;
        }
        return $years;
    }

    public function fetchPeriods()
    {
        return Period::orderBy('begin_date')->with('periodpattern')->get();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
                'name' => 'required|unique:periods',
                'begin_date' => 'required|date',
                'end_date' => 'required|date|after:begin_date',
                'pattern_id' => 'exists:period_patterns,id',
            ]
        );
        //$newperiod = Period::create($request->all());
        try {
            $newperiod = Period::create($request->all());
            return ['message' => 'Новая запись создана. Id:' . $newperiod->id];
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[0];
            // duplicate key value - код ошибки 7 при использовании PostgreSQL
            if($errorCode == '23505'){
                return ['error' => 422, 'message' => 'Новая запись не создана. Существует Период с такими же датами начала и окончания.'];
            }
        }
    }

    public function storeByPattern(Request $request) {
        $this->validate($request, [
                'year' => 'required|digits:4',
                'pattern_id' => 'required|exists:period_patterns,id',
            ]
        );
        $pattern = PeriodPattern::find($request->pattern_id);
        $period = new Period();
        $period->begin_date = $request->year . '-' . $pattern->begin; // Дата в ISO формате
        $period->end_date   = $request->year . '-' . $pattern->end;
        $period->name = $pattern->name . '. ' . $request->year . '.';
        $period->year = $request->year;
        $period->pattern_id = $request->pattern_id;
        try {
            $period->save();
            return ['message' => 'Новая запись создана. Id:' . $period->id];
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[0];
            switch ($errorCode) {
                // Код ошибки "Дублирующиеся значения" из PostgreSql
                case '23505':
                    $message = 'Запись не сохранена. Дублирующиеся значения.';
                    break;
                default:
                    $message = 'Новая запись не создана. Код ошибки ' . $errorCode . '.';
                    break;
            }
            return ['error' => 422, 'message' => $message];
        }
    }

    public function update(Request $request)
    {
        $this->validate($request, [
                'name' => 'required',
                'begin_date' => 'required|date',
                'end_date' => 'required|date|after:begin_date',
                'pattern_id' => 'exists:period_patterns,id',
            ]
        );
        $period = Period::find($request->id);
        $period->name = $request->name;
        $period->begin_date = $request->begin_date;
        $period->end_date = $request->end_date;
        $period->pattern_id = $request->pattern_id;
        $result = [];
        try {
            $period->save();
            $result = ['message' => 'Запись id ' . $period->id . ' сохранена'];
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[0];
            if($errorCode == '23505'){
                $result = ['error' => 422, 'message' => 'Запись не сохранена. Дублирование имени/дат отчетного периода.'];
            }
        }
        return $result;
    }

    public function delete(Period $period)
    {
        $id = $period->id;
        $doc_count = Document::countInPeriod($id);
        if ($doc_count == 0) {
            $period->delete();
            return ['message' => 'Удален отчетный период Id ' . $id ];
        } else {
            return ['error' => 422, 'message' => 'Период Id ' . $id . ' содержит документы. Удаление невозможно.' ];
        }
    }

    public function testOfWeeks($year)
    {
        set_time_limit(10);
        //setlocale(LC_TIME, 'ru-RU');
        //Carbon::setLocale('ru');
        $date = Carbon::parse($year . '-01-01');
        //echo $date->localeMonth;
        //echo  Carbon::getLocale();
        //var_dump($date->addMonth()->startOfMonth()->format('Y-m-d'));
        //var_dump($date->startOfWeek()->format('Y-m-d'));
        //var_dump($en->endOfWeek()->format('Y-m-d'));
        $w = 1;
        $stack = [];
        for ($i = 0 ; $i < 12 ; $i++) {
            // Вывод названия месяца в локализованном виде
            echo $date->formatLocalized('%B') . " -----------\n";
            $endmonth = $date->copy()->endOfMonth();
            $week = $date->copy()->startOfWeek();
            while (true) {
                $endOfMonth = false;
                $startweek = $week->copy()->startOfWeek();
                $endweek = $week->copy()->endOfWeek();
                $breaked = '';
                //var_dump($endmonth->eq($endweek));
                switch (true) {
                    case $startweek->month < $date->month || $startweek->year < $date->year :
                        $breaked = ' Неполная неделя';
                        $startweek->addWeek()->startOfMonth();
                        break;
                    case $endweek->month > $date->month || $endweek->year > $date->year :
                        $endweek = $startweek->copy()->endOfMonth();
                        $breaked = ' Неполная неделя';
                        $endOfMonth = true;
                        break;
                    case $endmonth->eq($endweek) :
                        $endOfMonth = true;
                        break;
                }

                $stack[] = [$startweek, $endweek];

                // Продолжительность недели, включая проследний день
                $duration = $startweek->diff($endweek)->days + 1;
                $breaked .= '(' . $duration . ')';
                echo $w . ' ' . $startweek->format('Y-m-d') . ' - ' . $endweek->format('Y-m-d') . $breaked .  " \n";
                $w++;
                if ($endOfMonth) {
                    break;
                }

                $week->addWeek();
            }
            $date->addMonth();
        }
        echo "-----------\n";
        dd($stack);
    }
}
