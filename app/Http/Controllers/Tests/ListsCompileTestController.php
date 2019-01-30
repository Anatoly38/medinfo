<?php

namespace App\Http\Controllers\Tests;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ListsCompileTestController extends Controller
{
    //
    public function listCompile()
    {
        $list = "u1003";
        $trimed = preg_replace('/,+\s+/u', ' ', $list);
        $lists = array_unique(array_filter(explode(' ', $trimed)));
        array_multisort($lists, SORT_NATURAL);
        $glued = implode(', ', $lists);
        $units = \App\Medinfo\DSL\FunctionCompiler::compileUnitList($lists);
        asort($units);
        $prop = '[' . implode(',', $units) . ']';
        $prophashed  =  crc32($prop);
        $scripthashed  =  sprintf("%u", crc32(preg_replace('/\s+/u', '', $glued)));
        dd($units);

    }
}
