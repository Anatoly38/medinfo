<?php
/**
 * Created by PhpStorm.
 * User: shameev
 * Date: 30.03.2019
 * Time: 8:49
 */

namespace App\Medinfo\ImportExport;


use Maatwebsite\Excel\Facades\Excel;
use App\Medinfo\ExcelExport;
use App\Document;
use App\Form;
use App\Unit;
use App\Period;
use App\Table;

class DocumentExcelExport
{
    public function batchExport($documents)
    {
        $docs = Document::find($documents);
        $random_suffix = str_random(8);
        $xls_files = [];
        foreach ($docs as $doc) {
            $dir = storage_path('app/exports/excel/' );
            //$fn = mb_ereg_replace("([^\w\d\-_~,;\[\]\(\).])", '_', $doc->unit->unit_name);
            //$fn = mb_ereg_replace("([\.]{2,})", '', $fn);
            //$fn = $doc->unit->unit_code. '_' . $fn . 'Форма_' . $doc->form->form_code . '_' .  $random_suffix;
            $fn = $doc->unit->unit_code . ' ' . $doc->unit->unit_name . ' Форма ' . $doc->form->form_code . ' Период ' . $doc->period->name ;
            $xls_files[] = $dir . $fn;
            $excelo = $this->documentExcelExport($doc, $fn);
            $excelo->store('xlsx', $dir);
        }
        $zip_file = storage_path('app/exports/excel/batchexcelexport_' . $random_suffix .'.zip');
        $zip = new \ZipArchive();
        if ($zip->open($zip_file, \ZipArchive::CREATE) === true) {
            $i = 1;
            foreach ($xls_files as $xls_file) {
                $zip->addFile($xls_file . '.xlsx', basename($xls_file) . '.xlsx');
                $i++;
            }
            $zip->close();
            foreach ($xls_files as $xls_file) {
              unlink($xls_file . '.xlsx');
            }
            return $zip_file;
        } else {
            throw  new \Exception("Не удалось открыть файл архива $zip_file");
        }

    }

    public function documentExcelExport(Document $document, string $filename = null)
    {
        $form = Form::find($document->form_id);
        $realform = Form::getRealForm($document->form_id);
        $ou = Unit::find($document->ou_id);
        $period = Period::find($document->period_id);
        $album = $document->album_id;
        $tables = Table::OfForm($realform->id)->whereDoesntHave('excluded', function ($query) use($album) {
            $query->where('album_id', $album);
        })->orderBy('table_index')->get();
        $outputname = $filename ? $filename : 'Form' . $form->form_code;
        $excel = Excel::create($outputname);
        $excel->sheet('Титул', function($sheet) use ($form, $ou, $period) {
            $sheet->cell('A1', function($cell) use ($ou){
                $cell->setValue('Учреждение: ' . $ou->unit_name);
                $cell->setFontSize(16);
            });
            $sheet->cell('A2', function($cell) use ($form){
                $cell->setValue('Форма: (' . $form->form_code . ') ' . $form->form_name);
                $cell->setFontSize(16);
            });
            $sheet->cell('A3', function($cell) use ($period){
                $cell->setValue('Период "' . $period->name . '"');
                $cell->setFontSize(16);
            });
            $sheet->cell('A4', function($cell) {
                $cell->setValue('Не для предоставления в МИАЦ в качестве отчетной формы!');
                $cell->setFontColor('#f00000');
                $cell->setFontSize(10);
            });
        });
        foreach ($tables as $table) {
            $ret = ExcelExport::getTableDataForExport($document, $table);
            $data = $ret['data'];
            $cols = $ret['cols'];
            $excel->sheet($form->form_code . '_' . $table->table_code , function($sheet) use ($table, $cols, $data) {
                $sheet->loadView('reports.datatable_excel_simple', compact('table', 'cols', 'data'));
                $sheet->cell('A1', function($cell) {
                    $cell->setFontSize(16);
                });
                $sheet->getStyle(ExcelExport::getCellByRC(1, 1))->getAlignment()->setWrapText(true);
                $sheet->getStyle(ExcelExport::getCellByRC(4, 1) . ':' . ExcelExport::getCellByRC(4, count($cols)))->getAlignment()->setWrapText(true);
                $sheet->getStyle(ExcelExport::getCellByRC(4, 1) . ':' . ExcelExport::getCellByRC(count($data)+5, count($cols)))->getBorders()
                    ->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
                $sheet->getStyle(ExcelExport::getCellByRC(4, 2) . ':' . ExcelExport::getCellByRC(count($data)+5, 2))->getNumberFormat()
                    ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            });
        }
        $excel->setActiveSheetIndex(0);
        return $excel;
    }

}