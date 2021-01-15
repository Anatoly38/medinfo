<?php

namespace App\Http\Controllers\ImportExport;

use App\Form;
use App\Table;
use Illuminate\Http\Request;

//use App\Http\Requests;
use App\Http\Controllers\Controller;

class MedstatExportController extends Controller
{
    const OFFSET = 4;
    //
    public function msExport(int $document)
    {
        setlocale(LC_ALL, 'us_US.UTF8');
        setlocale(LC_NUMERIC, 'C');
        $document = \App\Document::find($document);
        $form = $document->form;
        $unit = $document->unit;
        if (is_null($unit)) {
            $unit = $document->unitgroup;
            $code = $unit->group_code;
        } else {
            $code = $unit->unit_code;
        }
        $real = Form::getRealForm($form->id);
        $tables = $real->tables->sortBy('table_index')->filter(function ($table) {
            return !is_null($table->medstat_code);
        });

        $d = $this->initDBF($code, $form->medstat_code, $form->form_code);
        //dbase_add_record($db, $test_array);
        //dd(dbase_get_header_info($db));

        foreach ($tables as $table) {
            $rows = \App\Row::OfTable($table->id)->InMedstat()->get();
            if (!$table->transposed ) {
                foreach ($rows as $row) {
                    $insert_data = $d['pattern'];
                    if (\App\Cell::OfDTR($document->id, $table->id, $row->id)->sum('value')) {
                        $cells = \App\Cell::OfDTR($document->id, $table->id, $row->id)->get();
                        $insert_data[3] = '00' . $table->medstat_code;
                        $insert_data[4] = $row->medstat_code;
                        foreach ($cells as $cell) {
                            if (!is_null($cell->column->medstat_code)) {
                                $insert_data[(int)$cell->column->medstat_code + self::OFFSET] = (float)$cell->value;
                            }
                        }
                        //dbase_add_record($db, $insert_data);
                        try {
                            //dump($insert_data);
                            dbase_add_record($d['db'], $insert_data);
                        }
                        catch ( \ErrorException $e) {
                            dd($insert_data);
                        }
                    }
                }
            } elseif ($table->transposed == 1) {
                $insert_data = $d['pattern'];
                $insert_data[3] = '00' . $table->medstat_code;
                $insert_data[4] = '001';
                if (\App\Cell::OfDocumentTable($document->id, $table->id)->sum('value')) {
                    $cells = \App\Cell::OfDocumentTable($document->id, $table->id)->get();
                    foreach ($cells as $cell) {
                        if (!is_null($cell->row->medstat_code)) {
                            $insert_data[(int)$cell->row->medstat_code + self::OFFSET] = (float)$cell->value;
                        }
                    }
                    try {
                        //dump($insert_data);
                        dbase_add_record($d['db'], $insert_data);
                    }
                    catch ( \ErrorException $e) {
                        dd($insert_data);
                    }

                }
            }
        }
        return response()->download($d['file']);
    }

    public function tableMedstatExport(int $document, int $table)
    {
        //dd(localeconv());
        setlocale(LC_ALL, 'us_US.UTF8');
        setlocale(LC_NUMERIC, 'C');
        $document = \App\Document::find($document);
        $form = $document->form;
        $unit = $document->unit;
        if (is_null($unit)) {
            $unit = $document->unitgroup;
            $code = $unit->group_code;
        } else {
            $code = $unit->unit_code;
        }
        $table = \App\Table::find($table);
        $d = $this->initDBF($code, $form->medstat_code, $form->form_code);
        $rows = \App\Row::OfTable($table->id)->InMedstat()->OrderBy('medstat_code')->get();
        if (!$table->transposed ) {
            foreach ($rows as $row) {
                $insert_data = $d['pattern'];
                if (\App\Cell::OfDTR($document->id, $table->id, $row->id)->sum('value')) {
                    $cells = \App\Cell::OfDTR($document->id, $table->id, $row->id)->get();
                    $insert_data[3] = '00' . $table->medstat_code;
                    $insert_data[4] = $row->medstat_code;
                    foreach ($cells as $cell) {
                        if (!is_null($cell->column->medstat_code)) {
                            $insert_data[(int)$cell->column->medstat_code + self::OFFSET] = (float)$cell->value;
                        }
                    }
                    //dbase_add_record($db, $insert_data);
                    try {
                        //dump($insert_data);
                        dbase_add_record($d['db'], $insert_data);
                    }
                    catch ( \ErrorException $e) {
                        dd($insert_data);
                    }
                }
            }
        } elseif ($table->transposed == 1) {
            $insert_data = $d['pattern'];
            $insert_data[3] = '00' . $table->medstat_code;
            $insert_data[4] = '001';
            if (\App\Cell::OfDocumentTable($document->id, $table->id)->sum('value')) {
                $cells = \App\Cell::OfDocumentTable($document->id, $table->id)->get();
                foreach ($cells as $cell) {
                    if (!is_null($cell->row->medstat_code)) {
                        $insert_data[(int)$cell->row->medstat_code + self::OFFSET] = (float)$cell->value;
                    }
                }
                try {
                    //dump($insert_data);
                    dbase_add_record($d['db'], $insert_data);
                }
                catch ( \ErrorException $e) {
                    dd($insert_data);
                }

            }
        }
        return response()->download($d['file']);
    }

    public function initDBF($ucode, $mscode, $fcode)
    {
        $a1_code = config('medinfo.year_code'); // код отчетного года
        $a2_code = config('medinfo.terr_code'); // код территории
        $a4_code = $mscode . '00'; // код формы
        $offset = 4; // сдвиг до индекса массива, где начинаются данные ячеек

        $medstatsructure = [
            ["a1", "C", 2],
            ["a2", "C", 4],
            ["a4", "C", 7],
            ["a5", "C", 6],
            ["a6", "C", 3],
            ["a81", "N", 12, 2],
            ["a82", "N", 12, 2],
            ["a83", "N", 12, 2],
            ["a84", "N", 12, 2],
            ["a85", "N", 12, 2],
            ["a86", "N", 12, 2],
            ["a87", "N", 12, 2],
            ["a88", "N", 12, 2],
            ["a89", "N", 12, 2],
            ["a810", "N", 12, 2],
            ["a811", "N", 12, 2],
            ["a812", "N", 12, 2],
            ["a813", "N", 12, 2],
            ["a814", "N", 12, 2],
            ["a815", "N", 12, 2],
            ["a816", "N", 12, 2],
            ["a817", "N", 12, 2],
            ["a818", "N", 12, 2],
            ["a819", "N", 12, 2],
            ["a820", "N", 12, 2],
            ["a821", "N", 12, 2],
            ["a822", "N", 12, 2],
            ["a823", "N", 12, 2],
            ["a824", "N", 12, 2],
            ["a825", "N", 12, 2],
            ["a826", "N", 12, 2],
            ["a827", "N", 12, 2],
            ["a828", "N", 12, 2],
            ["a829", "N", 12, 2],
            ["a830", "N", 12, 2],
            ["a831", "N", 12, 2],
            ["a832", "N", 12, 2],
            ["a833", "N", 12, 2],
            ["a834", "N", 12, 2],
            ["a835", "N", 12, 2],
            ["a836", "N", 12, 2],
            ["a837", "N", 12, 2],
            ["a838", "N", 12, 2],
            ["a839", "N", 12, 2],
            ["a840", "N", 12, 2],
            ["a841", "N", 12, 2],
            ["a842", "N", 12, 2],
            ["a843", "N", 12, 2],
            ["a844", "N", 12, 2],
            ["a845", "N", 12, 2],
            ["a846", "N", 12, 2],
            ["a847", "N", 12, 2],
            ["a848", "N", 12, 2],
            ["a849", "N", 12, 2],
            ["a850", "N", 12, 2],
            ["srt", "C", 25],
            ["n1", "N", 2, 0],
            ["n2", "N", 2, 0],
        ];

        $insert_pattern_no_works = [
            'a1' => $a1_code,
            'a2' => $a2_code,
            'a4' => $a4_code,
            'a5' => '',
            'a6' => '',
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0,
            7 => 0,
            8 => 0,
            9 => 0,
            10 => 0,
            11 => 0,
            12 => 0,
            13 => 0,
            14 => 0,
            15 => 0,
            16 => 0,
            17 => 0,
            18 => 0,
            19 => 0,
            20 => 0,
            21 => 0,
            22 => 0,
            23 => 0,
            24 => 0,
            25 => 0,
            26 => 0,
            27 => 0,
            28 => 0,
            'srt' => '',
            'n1' => 0,
            'n2' => 0,
        ];

        $insert_pattern = [
            $a1_code, //0
            $a2_code, // 1
            $a4_code, // 2
            '', // 3 - код таблицы
            '', //4 - код строки
            0, //5  - первая графа
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            '',
            0,
            0,
        ];

        // создаем
        $dbf_name = $ucode . '_' . $fcode . '.dbf';
        $dbf_file = storage_path('app/exports/medstat') . '/' . $dbf_name;
        if (file_exists($dbf_file)) {
            unlink($dbf_file);
        }

        $db = dbase_create($dbf_file, $medstatsructure);
        if (!$db) {
            new \Exception("Ошибка, не получается создать базу данных " . $dbf_name);
        }
        return [ 'pattern' => $insert_pattern, 'file' => $dbf_file, 'db' => $db ];
    }

}
