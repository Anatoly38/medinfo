<?php
/**
 * Created by PhpStorm.
 * User: shameev
 * Date: 29.05.2018
 * Time: 17:38
 */

namespace App\Medinfo\DSL;


use Illuminate\Support\Collection;

class FunctionCompiler
{

    public static function compileRule($script, \App\Table $table)
    {
        try {
            $lexer = new \App\Medinfo\DSL\ControlFunctionLexer($script);
            $tockenstack = $lexer->getTokenStack();
            $parser = new \App\Medinfo\DSL\ControlFunctionParser($tockenstack);
            $parser->func();
            $translator = \App\Medinfo\DSL\Translator::invoke($parser, $table);
            $translator->prepareIteration();
            $compiled_cache['ptree'] = base64_encode(serialize($translator->parser->root));
            $compiled_cache['properties'] = $translator->getProperties();
        } catch (\Exception $e) {
            $compiled_cache['compile_error'] = "Ошибка при компилляции функции: " . $e->getMessage();
        }
        return $compiled_cache;
    }

    public static function compileUnitList(array $lists, array $levelunits = [])
    {
        $addlists = [];
        $subtractlists = [];
        $limitationlists = [];
        $units = [];
        $addunits = [];
        $subtractunits = [];
        $limitationunits = [];
        //dd($lists);
        foreach ($lists as $list) {
            $prefix = $list[0];
            switch ($prefix) {
                case '!' :
                    $list = substr($list, 1);
                    $subtractlists[] = $list;
                    break;
                case '~' :
                    $list = substr($list, 1);
                    $limitationlists[] = $list;
                    break;
                default:
                    $addlists[] = $list;
            }
        }
        try {
            $addunits = array_merge($addunits, self::getUnitsFromLists($addlists));
            $subtractunits = array_merge($subtractunits, self::getUnitsFromLists($subtractlists));
            foreach($limitationlists as $limitationlist) {
                $limitationunits[] = self::getUnitsFromLists([$limitationlist]);
            }
        }
        catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
        $addunits = array_unique($addunits);
        $subtractunits = array_unique($subtractunits);
        //$limitationunits = array_unique($limitationunits);
        //dd($limitationlists);
        //dd($limitationunits);
        $units = array_diff($addunits, $subtractunits);
         foreach ($limitationunits as $limit) {
            $units = array_intersect($units, $limit);
        }
        // Если список учреждений полученнный от уровня консолидированного документа не пуст - ограничиваем вывод и по нему тоже
        if (count($levelunits) > 0) {
            $units = array_intersect($units, $levelunits);
        }
        //return \App\Unit::whereIn('id', $units)->get(['id', 'unit_code', 'unit_name'])->sortBy('unit_code');
        return $units;
    }

    public static function getUnitsFromLists(array $lists)
    {
        $units = [];
        foreach ($lists as $list) {
            switch (true) {
                case $list[0] === 'u':
                    $unitcode = substr($list, 1);
                    $units = array_merge($units, self::getUnitsFromTree($unitcode));
                    break;
                case in_array($list, config('medinfo.reserved_unitlist_slugs')):
                    $units = array_merge($units, self::getUnitsFromReserved($list));
                    break;
                default:
                    $u = \App\UnitList::Slug($list)->first();
                    if (is_null($u)) {
                        throw new \Exception("Список '$list' не существует");
                    }
                    $units = array_merge($units, $u->members->pluck('ou_id')->toArray());
            }

        }
        return $units;
    }

    public static function getUnitsFromReserved(string $staticlist)
    {
        switch ($staticlist) {
            case '*' :
            case 'все' :
                // Выбирает только МО юрлица и ОП, учреждения соц. защиты и образования не включаются
                $units = \App\Unit::Active()->MedicalUnits()->pluck('id')->toArray();
                break;
            case 'юл' :
            case 'юрлица' :
                $units = \App\Unit::Active()->Legal()->get()->pluck('id')->toArray();
                break;
            case 'оп' :
            case 'обособподр' :
                $units = \App\Unit::Active()->SubLegal()->get()->pluck('id')->toArray();
                break;
            case 'село' :
                $units = \App\Unit::Active()->Country()->get()->pluck('id')->toArray();
                break;
            default :
                throw new \Exception("Статический список/группа '$staticlist' не существует");
        }
        return $units;
    }

    public static function getUnitsFromTree(string $unitcode) {
        $unit = \App\Unit::Code($unitcode)->first();
        if (!$unit) {
            throw new \Exception("Не найдена ОЕ с кодом $unitcode" );
        }
        if ($unit->node_type === 3 || $unit->node_type === 4) {
            return [$unit->id];
        }
        $units = \App\Unit::getPrimaryDescendants($unit->id);
        return collect($units)->pluck('id')->toArray();
    }

}