<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    // массив для поиска паттерна предыдущего периода
    public static $period_cycles_backward = [
        1 => 1, // годовые
        2 => 5, 3 => 2, 4 => 3, 5 => 4, // квартальные
        6 => 9, 7 => 6, 8 => 7, 9 => 8, // квартальные накопительные
        10 => 11, 11 => 10, // полугодовые
        13 => 23, 14 => 13, 15 => 14, 16 => 15, 17 => 16, 18 => 17, 12 => 18, 19 => 12, 20 => 19, 21 => 20, 22 => 21, 23 => 22, // месячные
    ];
    protected $fillable = ['name', 'year', 'begin_date', 'end_date', 'pattern_id', ];
    protected $dates = ['begin_date', 'end_date',];

    public function periodpattern()
    {
        return $this->belongsTo('App\PeriodPattern', 'pattern_id');
    }

    public function scopeLastYear($query)
    {
        $date = ((int)date("Y") - 1 ) . '-01-01';
        return $query
            ->where('begin_date', $date)
            ->where('pattern_id', 1); // 1 - Паттерн годового отчетного периода
    }

    public function scopePreviousYear($query, $current_year)
    {
        $date = ((int)$current_year - 1 ) . '-01-01';
        return $query
            ->where('begin_date', $date)
            ->where('pattern_id', 1); // 1 - Паттерн годового отчетного периода
    }

    public function scopePreviousAnnual($query, $current_period)
    {
        $previous_annual_pattern = self::$period_cycles_backward[$current_period->pattern_id];
        $previous_annual_enddate = $current_period->end_date->subYear(); // Функция Carbon, вычитающая год из текущей даты
        return $query
            ->where('end_date', $previous_annual_enddate)
            ->where('pattern_id', $previous_annual_pattern);
    }

    public function scopePreviousSemiannual($query, $current_period)
    {
        $previous_semiannual_pattern = self::$period_cycles_backward[$current_period->pattern_id];
        $previous_semiannual_enddate = $current_period->end_date->subYear(); // Функция Carbon, вычитающая год из текущей даты
        return $query
            ->where('end_date', $previous_semiannual_enddate)
            ->where('pattern_id', $previous_semiannual_pattern);
    }

    public function scopePreviousQuarter($query, $current_period)
    {
        $previous_quarter_pattern = self::$period_cycles_backward[$current_period->pattern_id];
        $previous_quarter_enddate = $current_period->end_date->subQuarter()->subDay()->endOfMonth(); // Функция Carbon, вычитающая квартал из текущей даты
        return $query
            ->where('end_date', $previous_quarter_enddate)
            ->where('pattern_id', $previous_quarter_pattern);
    }

    public function scopePreviousMonth($query, $current_period)
    {
        $previous_month_pattern = self::$period_cycles_backward[$current_period->pattern_id];
        $previous_month_enddate = $current_period->end_date->subMonth()->subDay()->endOfMonth(); // Функция Carbon, вычитающая месяц из текущей даты
        return $query
            ->where('end_date', $previous_month_enddate)
            ->where('pattern_id', $previous_month_pattern);
    }

}

