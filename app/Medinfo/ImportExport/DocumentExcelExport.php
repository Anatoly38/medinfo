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
            $fn = $doc->id . '_' . $random_suffix;
            $xls_files[] = $dir . $fn;
            $excelo = $this->documentExcelExport($doc, $fn);
            $excelo->store('xls', $dir);
        }

        $zip_file = storage_path('app/exports/excel/batchexcelexport' . $random_suffix .'.zip');
        $zip = new \ZipArchive();
        if ($zip->open($zip_file, \ZipArchive::CREATE) === true) {
            //$zip->addFromString("testfilephp.txt" . time(), "#1 Это тестовая строка добавляется в качестве testfilephp.txt.\n");
            //$zip->addFromString("testfilephp2.txt" . time(), "#2 Это тестовая строка добавляется в качестве testfilephp2.txt.\n");
            //$zip->addFile($thisdir . "/too.php","/testfromfile.php");
            //echo "numfiles: " . $zip->numFiles . "\n";
            //echo "status:" . $zip->status . "\n";
            $i = 1;
            foreach ($xls_files as $xls_file) {
                $zip->addFile($xls_file . '.xls', $i . '.xls');
                $i++;
            }
            $zip->close();
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
                $sheet->loadView('reports.datatable_excel', compact('table', 'cols', 'data'));
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