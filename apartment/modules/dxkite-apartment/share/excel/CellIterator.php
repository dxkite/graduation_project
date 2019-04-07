<?php
namespace dxkite\apartment\excel;

use PhpOffice\PhpSpreadsheet\Collection\Cells;

class CellIterator implements \Iterator
{
    private $cells;
    private $position = 0;
    private $highest='A';

    public function __construct(Cells $cells,string $highest)
    {
        $this->cells=$cells;
        $this->highest=$highest;
    }

    public function setPosition(int $p)
    {
        $this->position = $p;
    }
    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        $columns=range('A', $this->highest);
        $cell=[];
        foreach ($columns as $column) {
            $cell[$column]=$this->cells->get($column.($this->position+1))->getValue();
        }
        return $cell;
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        $cell=$this->highest.($this->position+1);
        $has_cell=$this->cells->has($cell);
        return $has_cell;
    }
}
