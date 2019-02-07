<?php
/**
 * Created by PhpStorm.
 * User: shameev
 * Date: 13.12.2017
 * Time: 17:59
 */

namespace App\Medinfo\DSL;

use App\Unit;

class ValueCountEvaluator extends CalculationFunctionEvaluator
{
    public function setArguments()
    {
        $this->getArgument(1);
        $this->getArgument(2);
    }

    public function makeConsolidation()
    {
        if (!$this->arguments[2]->content) {
            $this->countAllUnits();
        } else {
            $this->countOnlyLegalUnits();
        }
    }

    public function  countAllUnits()
    {
        $this->calculatedValue = 0;
        $this->clearCalculationLog();
        $period_id = $this->document->period_id;
        $this->prepareCAstack();
        //dd($this->properties);
        foreach ($this->properties['units'] as $ou_id) {
            foreach ($this->iterations[0] as &$cell_adress) {
                $document = \App\Document::Primary()->OfUPF($ou_id, $period_id, $cell_adress['ids']['f'])->first();
                $cell = $document ? \App\Cell::OfDRC($document->id, $cell_adress['ids']['r'], $cell_adress['ids']['c'])->first(['value']) : null;
                !$cell ? $value = 0 : $value = (float)$cell->value;
                $cell_adress['value'] = $value;
            }
            $this->convertCANodes($this->iterations[0]);
            $calculated = $this->evaluateSubtree($this->arguments[1]);
            $this->logIteration($ou_id, $calculated);
            if ($calculated > 0) {
                $this->calculatedValue++;
            }
        }
    }

    public function countOnlyLegalUnits()
    {
        $this->calculatedValue = 0;
        $this->clearCalculationLog();
        $period_id = $this->document->period_id;
        $this->prepareCAstack();
        //dd($this->properties);
        //dd($this->properties['units']);
        foreach ($this->properties['units'] as $ou_id) {
            $unit = Unit::find($ou_id);
            if ($unit->node_type === 4) {
                continue;
            }
            if ($unit->aggregate === 0) {
                foreach ($this->iterations[0] as &$cell_adress) {
                    $document = \App\Document::Primary()->OfUPF($unit->id, $period_id, $cell_adress['ids']['f'])->first();
                    $cell = $document ? \App\Cell::OfDRC($document->id, $cell_adress['ids']['r'], $cell_adress['ids']['c'])->first(['value']) : null;
                    !$cell ? $value = 0 : $value = (float)$cell->value;
                    $cell_adress['value'] = $value;
                }
            } else {
                $descendants = Unit::getDescendants($unit->id);
                foreach ($this->iterations[0] as &$cell_adress) {
                    $documents = \App\Document::Primary()->OfPF($period_id, $cell_adress['ids']['f'])->OfUnits($descendants)->pluck('id')->toArray();
                    $cell = count($documents) > 0 ? \App\Cell::OfRC($cell_adress['ids']['r'], $cell_adress['ids']['c'])->OfDocuments($documents)->sum('value') : null;
                    !$cell ? $value = 0 : $value = (float)$cell;
                    $cell_adress['value'] = $value;
                }
            }
            $this->convertCANodes($this->iterations[0]);
            $calculated = $this->evaluateSubtree($this->arguments[1]);
            $this->logIteration($unit->id, $calculated);
            if ($calculated > 0) {
                $this->calculatedValue++;
            }
        }
    }

    public function evaluate()
    {
        return $this->calculatedValue;
    }
}