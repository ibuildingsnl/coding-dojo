<?php

namespace CodingDojo;

final class GenerationPrinter
{
    public function print(array $grid): string
    {
        $generation = '';

        foreach ($grid as $row) {
            foreach ($row as $cell) {
                $renderedCell = $cell ? '#' : ' ';
                $generation .= $renderedCell;
            }
            $generation .= PHP_EOL;
        }

        return $generation;
    }
}
