<?php
/**
 * Created by PhpStorm.
 * User: shameev
 * Date: 13.12.2017
 * Time: 17:59
 */

namespace App\Medinfo\DSL;

use App\Unit;

class IntervalEvaluator extends CalculationFunctionEvaluator
{
    public function setArguments()
    {
        $this->getArgument(1);
        $this->getArgument(2);
        $this->getArgument(3);
    }

    public function makeConsolidation()
    {
        //dd($this->arguments);
        $this->calculatedValue = 0;
        $this->clearCalculationLog();
        $period_id = $this->document->period_id;
        $this->prepareCAstack();
        foreach ($this->properties['units'] as $ou_id) {
            $unit = Unit::find($ou_id);
            if ($unit->node_type === 3) {
                $unit_scope = Unit::getDescendants($unit->id);
                foreach ($this->iterations[0] as &$cell_adress) {
                    $documents = \App\Document::Primary()->OfPF($period_id, $cell_adress['ids']['f'])->OfUnits($unit_scope)->pluck('id')->toArray();
                    $cell_value = count($documents) > 0 ? \App\Cell::OfDocuments($documents)->OfRC($cell_adress['ids']['r'], $cell_adress['ids']['c'])->sum('value') : null;
                    !$cell_value ? $value = 0 : $value = (float)$cell_value;
                    $cell_adress['value'] = $value;
                }

                $cells = $this->convertCANodes($this->iterations[0]);
                $unitValue = $this->evaluateSubtree($this->arguments[1]);
                if ($this->between($unitValue, $this->arguments[2]->content, $this->arguments[3]->content) ) {
                    $this->logIteration($ou_id, $unitValue);
                    $this->calculatedValue++;
                }
            }
        }
    }

    public function evaluate()
    {
        return $this->calculatedValue;
    }


}