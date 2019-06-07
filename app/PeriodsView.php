<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PeriodsView extends Model
{
    //
    protected $table = 'periods_view';

    public function periodpattern()
    {
        return $this->belongsTo('App\PeriodPattern', 'pattern_id');
    }

    public function documents()
    {
        return $this->hasMany('App\Document', 'period_id', 'id');
    }
}
