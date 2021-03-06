<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnitListMember extends Model
{
    //
    protected $fillable = ['list_id', 'ou_id' ];

    public function unitlist()
    {
        return $this->belongsTo('App\UnitList' , 'list_id', 'id' );
    }

    public function unit()
    {
        return $this->belongsTo('App\Unit', 'ou_id', 'id');
    }

    public function scopeList($query, $list)
    {
        return $query->where('list_id', $list);
    }

    public function scopeNotOfList($query, $list)
    {
        return $query->where('list_id','<>', $list);
    }

    public function scopeOfUnit($query, $unit)
    {
        return $query->where('ou_id', $unit);
    }
}
