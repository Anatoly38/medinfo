<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    //
    protected $table = 'mo_hierarchy';
    protected $fillable = [
        'parent_id', 'unit_code','territory_type' ,'inn', 'node_type', 'report', 'aggregate', 'unit_name',
        'blocked', 'countryside', 'adress',
    ];

    public function workerScope()
    {
        return $this->hasMany('App\WorkerScope', 'ou_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Unit', 'parent_id', 'id');
    }

    public function groups()
    {
        return $this->hasMany('App\UnitListMember', 'ou_id', 'id');
    }

    public function lists()
    {
        return $this->hasMany('App\UnitListMember', 'ou_id', 'id');
    }
    // Выбор уровня "Все организации"
    public function scopeRoot($query)
    {
        return $query->where('id', 0);
    }
    // Выбор ОЕ по коду
    public function scopeCode($query, $unit_code)
    {
        return $query->where('unit_code', $unit_code);
    }
    // Выбор Территорий
    public function scopeTerritory($query)
    {
        return $query->where('node_type', 2);
    }
    // Выбор Юрлиц
    public function scopeLegal($query)
    {
        return $query->where('node_type', 3);
    }
    // Выбор Обособленных подразделений
    public function scopeSubLegal($query)
    {
        return $query->where('node_type', 4);
    }
    // Выбор медицинских подразделений
    public function scopeMedicalUnits($query)
    {
        return $query->where('node_type', 3)->orWhere('node_type', 4);
    }
    // Выбор выделенных подразделений
    public function scopeSubDivision($query)
    {
        return $query->where('node_type', 5);
    }
    // Выбор обособленных и выделенных подразделений
    public function scopeSubUnits($query)
    {
        return $query->where('node_type', 4)->orWhere('node_type', 5);
    }

    // Выбор учреждений образования и социальной защиты
    public function scopeSchoolAndSocial($query)
    {
        return $query->where('node_type', 6);
    }
    // Выбор медицинских училищ
    public function scopeColleges($query)
    {
        return $query->where('node_type', 7);
    }
    // Все подразделений с первичными отчетами за исключением медицинских училищ
    public function scopePrimary($query)
    {
        return $query->where('node_type', 3)->orWhere('node_type', 4)->orWhere('node_type', 6);
    }

    public function scopeUpperLevels($query)
    {
        return $query->where('node_type', 1)->orWhere('node_type', 2);
    }

    // Только незаблокированные единицы
    public function scopeActive($query)
    {
        return $query->where('blocked', 0);
    }
    // Единицы по которым может производится сведение данных
    public function scopeMayBeAggregate($query)
    {
        return $query
            ->where('aggregate', 1);
    }

    public function scopeCountry($query)
    {
        return $query
            ->where('countryside', true);
    }

    public function scopeChilds($query, $parent)
    {
        return $query
            ->where('parent_id', $parent);
    }

    public static function getDescendants($parent) {
        $units[] = (int)$parent;
        $lev_query = "select id from mo_hierarchy where parent_id = $parent";
        $res = \DB::select($lev_query);
        if (count($res) > 0) {
            foreach ($res as $r) {
                $units = array_merge($units, self::getDescendants($r->id));
            }
        }
        return $units;
    }

    public static function getPrimaryDescendants($parent, $with_blocked = 0) {
        $units = [];
        $lev_query = "SELECT * FROM mo_hierarchy WHERE parent_id = $parent AND blocked = $with_blocked";
        $res = \DB::select($lev_query);
        if (count($res) > 0) {
            foreach ($res as $r) {
                if ($r->node_type == 3 || $r->node_type == 4) {
                    $units[] = Unit::find($r->id);
                }
                if ($r->aggregate) {
                    $units = array_merge($units, self::getPrimaryDescendants($r->id));
                }
            }
        }
        return $units;
    }

}