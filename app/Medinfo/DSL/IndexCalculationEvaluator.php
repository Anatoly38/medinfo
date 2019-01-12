<?php
/**
 * Created by PhpStorm.
 * User: shameev
 * Date: 13.12.2017
 * Time: 17:59
 */

namespace App\Medinfo\DSL;


class IndexCalculationEvaluator extends CalculationFunctionEvaluator
{
    public function setArguments()
    {
        $this->getArgument(1);
    }

    public function makeConsolidation()
    {
        $this->calculatedValue = 0;
        $this->clearCalculationLog();
        $period_id = $this->document->period_id;
        $this->prepareCAstack();
        // Находим сперва ссылки на документ с показателями на уровне текущего документа
        foreach ($this->iterations[0] as &$cell_adress) {
            $document = \App\Document::Indexes()->OfUPF($this->document->ou_id, $period_id, $cell_adress['ids']['f'])->first();
            //dump($document);
            $cell = $document ? \App\Cell::OfDRC($document->id, $cell_adress['ids']['r'], $cell_adress['ids']['c'])->first(['value']) : null;
            !$cell ? $value = 0 : $value = (float)$cell->value;
            $cell_adress['value'] = $value;
        }
        $this->convertCANodes($this->iterations[0]);
        foreach ($this->iterations[0] as &$cell_adress) {
            foreach ($this->properties['units'] as $ou_id) {
                $document = \App\Document::Primary()->OfUPF($ou_id, $period_id, $cell_adress['ids']['f'])->first();
                $cell = $document ? \App\Cell::OfDRC($document->id, $cell_adress['ids']['r'], $cell_adress['ids']['c'])->first(['value']) : null;
                !$cell ? $value = 0 : $value = (float)$cell->value;
                $cell_adress['value'] += $value;
                if ($value) {
                    $this->logIteration($ou_id, $value, $cell_adress['codes']);
                }
            }
        }
        $this->convertCANodes($this->iterations[0]);
        $this->calculatedValue = $this->evaluateSubtree($this->arguments[1]);

    }

    public function evaluate()
    {
        return $this->calculatedValue;
    }

    public function logIteration($ou_id, $value, $cell = null)
    {
        $this->calculationLog[] = ['unit_id' => $ou_id, 'value' => $value, 'cell' => $cell];
    }

}