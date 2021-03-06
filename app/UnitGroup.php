<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnitGroup extends Model
{
    //
    protected $fillable = ['parent_id', 'group_code', 'group_name', 'slug'];
    // зарезервированные имена для "статических" групп
    public static $reserved_slugs = [
        'n/a',
        'первичные',
        'сводные',
        'оп',
        'обособподр',
        'юл',
        'юрлица',
        'тер',
        'территории',
        'п0', 'п1', 'п2', 'п3', 'п4', 'п5', 'п6', 'п7', 'п8', 'п9', 'п10', 'п11', 'п12', // 9-21 периоды месячные
        'пi', 'пii', 'пiii', 'пiv', // 22-25 периоды квартальные
    ];
    const PRIMARY   = 1;
    const AGGREGATE = 2;
    const DETACHED  = 3;
    const LEGAL     = 5;
    const TERRITORY = 7;

    public function members()
    {
        return $this->hasMany('App\UnitGroupMember', 'group_id', 'id');
    }

    public function scopeOfSlug($query, $slug)
    {
        return $query
            ->where('slug', $slug);
    }
}
