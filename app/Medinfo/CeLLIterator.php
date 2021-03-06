<?php
/**
 * Created by PhpStorm.
 * User: shameev
 * Date: 15.07.2016
 * Time: 19:56
 */

namespace App\Medinfo;
use App\Table;
use App\Row;
use App\Column;
use App\Album;

class CeLLIterator
{
    //public $table_id;
    public $current_cell = 0;
    private $_end = false;
    private $_ou_id;
    private $_form_id;
    private $_period;
    private $table;
    private $data_only_cells = true;
    private $_rows = array();
    private $_columns = array();
    private $_all_cells = array();

    public function __construct(Table $table)
    {
        $this->table = $table;
        $default_album = Album::Default()->first(['id']);
        if (!$default_album) {
            $default_album = Album::find(config('medinfo.default_album'));
        }
        //$this->_rows = $this->table->rows->where('deleted', 0)->sortBy('row_index');

        $this->_rows = Row::OfTable($table->id)->whereDoesntHave('excluded', function ($query) use($default_album) {
            $query->where('album_id', $default_album->id)->orderBy('row_index');
        })->get();

        //$this->_columns = $this->table->columns->where('deleted', 0)->sortBy('column_index');
        $this->_columns = Column::OfTable($table->id)->whereDoesntHave('excluded', function ($query) use($default_album) {
            $query->where('album_id', $default_album->id)->orderBy('column_index');
        })->get();


        $this->setCollection();
        //dd($this->_all_cells);
    }

    public static function create($table)
    {
        return new CeLLIterator($table);
    }

    public function setDataOnlyCells($state = false)
    {
        $this->data_only_cells = $state;
    }

/*    public function setDocumentId($doc_id = null)
    {
        $this->_doc_id = $doc_id;
    }*/

    public function setOu($ou = null)
    {
        $this->_ou_id = $ou;
    }

    public function setForm($form)
    {
        $this->_form_id = $form;
    }

    public function setPeriod($period)
    {
        $this->_period = $period;
    }

    public function current()
    {
        return $this->_all_cells[$this->current_cell];
    }

    public function first()
    {
        $this->current_cell = 0;
        return $this->_all_cells[0];
    }

    public function last()
    {
        $this->_end = true;
        $this->current_cell = count($this->_all_cells) - 1;
        return $this->_all_cells[$this->current_cell];
    }

    public function next()
    {
        if ($this->_end) {
            return false;
        }
        $offset = $this->current_cell + 1;
        if (isset($this->_all_cells[$offset])) {
            ++$this->current_cell;
            return $this->_all_cells[$offset];
        }
        else {
            $this->_end = true;
            return false;
        }
    }

    public function prev()
    {
        if ($this->current_cell == 0) {
            return false;
        }
        --$this->current_cell;
        return $this->_all_cells[$this->current_cell];
    }

    public function setCollection()
    {
        $this->_all_cells = array();
        $i = 0;
        //dd($this->_columns);
        foreach ($this->_rows as $r) {
            foreach ($this->_columns as $c) {
                //$cell_adress = 'D'. $this->_doc_id .'T'. $this->table->id .'R'. $r['row_id'] .'C'. $c['col_id'];
                if ($this->data_only_cells) {
                    if ($c->getMedinfoContentType() == 'data') {
                        //$this->_all_cells[$i] = array('t' => $this->table->id, 'r' => $r['row_id'], 'c' => $c['col_id'], 'adress' => $cell_adress);
                        $this->_all_cells[$i] = ['t' => $this->table->id, 'r' => $r->id, 'c' => $c->id, ];
                        $i++;
                    }
                }
                else {
                    //$this->_all_cells[$i] = array('t' => $this->table_id, 'r' => $r['row_id'], 'c' => $c['col_id'], 'oftrcp' => $cell_adress);
                    $this->_all_cells[$i] = ['t' => $this->table->id, 'r' => $r->id, 'c' => $c->id, ];
                    $i++;
                }


            }
        }
        $this->current_cell = 0;
        //if (count($this->_all_cells == 0)) {
          //  throw new \Exception("В текущей таблице \"" . $this->table->id . "\" нет ячеек с данными для итерации");
        //}
        return $this->_all_cells;
    }

}