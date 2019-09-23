<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PeriodPattern extends Model
{
    //
    protected $fillable = ['name', 'periodicity', 'begin', 'end'];

    public function periodicity()
    {
        return $this->belongsTo('App\DicPeriodicity', 'periodicity', 'code');
    }

    public function scopeYear($query)
    {
        return $query
            //->where('periodicity', 1)
            ->where('begin', '01-01')
            ->where('end', '12-31');
    }

    public function scopeI($query)
    {
        return $query
            ->where('periodicity', 3)
            ->where('begin', '01-01')
            ->where('end', '03-31');
    }

    public function scopeII($query)
    {
        return $query
            ->where('periodicity', 3)
            ->where('begin', '04-01')
            ->where('end', '06-30');
    }

    public function scopeIII($query)
    {
        return $query
            ->where('periodicity', 3)
            ->where('begin', '07-01')
            ->where('end', '09-30');
    }

    public function scopeIV($query)
    {
        return $query
            ->where('periodicity', 3)
            ->where('begin', '10-01')
            ->where('end', '12-31');
    }

    public function scopeIplus($query)
    {
        return $query
            ->where('periodicity', 4)
            ->where('begin', '01-01')
            ->where('end', '03-31');
    }

    public function scopeIIplus($query)
    {
        return $query
            ->where('periodicity', 4)
            ->where('begin', '01-01')
            ->where('end', '06-30');
    }

    public function scopeIIIplus($query)
    {
        return $query
            ->where('periodicity', 4)
            ->where('begin', '01-01')
            ->where('end', '09-30');
    }

    public function scopeIVplus($query)
    {
        return $query
            ->where('periodicity', 4)
            ->where('begin', '01-01')
            ->where('end', '12-31');
    }

    public function scopeM1($query)
    {
        return $query
            ->where('periodicity', 5)
            ->where('begin', '01-01')
            ->where('end', '01-31');
    }

    public function scopeM2($query)
    {
        return $query
            ->where('periodicity', 5)
            ->where('begin', '02-01')
            ->where('end', '02-29');
    }

    public function scopeM3($query)
    {
        return $query
            ->where('periodicity', 5)
            ->where('begin', '03-01')
            ->where('end', '03-31');
    }

    public function scopeM4($query)
    {
        return $query
            ->where('periodicity', 5)
            ->where('begin', '04-01')
            ->where('end', '04-30');
    }

    public function scopeM5($query)
    {
        return $query
            ->where('periodicity', 5)
            ->where('begin', '05-01')
            ->where('end', '05-31');
    }

    public function scopeM6($query)
    {
        return $query
            ->where('periodicity', 5)
            ->where('begin', '06-01')
            ->where('end', '06-30');
    }

    public function scopeM7($query)
    {
        return $query
            ->where('periodicity', 5)
            ->where('begin', '07-01')
            ->where('end', '07-31');
    }

    public function scopeM8($query)
    {
        return $query
            ->where('periodicity', 5)
            ->where('begin', '08-01')
            ->where('end', '08-31');
    }

    public function scopeM9($query)
    {
        return $query
            ->where('periodicity', 5)
            ->where('begin', '09-01')
            ->where('end', '09-30');
    }

    public function scopeM10($query)
    {
        return $query
            ->where('periodicity', 5)
            ->where('begin', '10-01')
            ->where('end', '10-31');
    }

    public function scopeM11($query)
    {
        return $query
            ->where('periodicity', 5)
            ->where('begin', '11-01')
            ->where('end', '11-30');
    }

    public function scopeM12($query)
    {
        return $query
            ->where('periodicity', 5)
            ->where('begin', '12-01')
            ->where('end', '12-31');
    }

}
