<?php

namespace CodingDojo;

use InvalidArgumentException;
use RuntimeException;

final class GenerationLoader
{
    public static function fromText($string)
    {
        $result = [];
        $lines  = explode("\n", $string);

        $dimensions = array_shift($lines);
        if (!preg_match('~^(\d+)x(\d+)$~', $dimensions, $matches)) {
            throw new InvalidArgumentException('Columns/rows should be numeric');
        }
        $numberOfColumns = (int) $matches[1];
        $numberOfRows    = (int) $matches[2];
        $rows            = array_values($lines);

        if (count($rows) !== $numberOfRows) {
            throw new InvalidArgumentException(
                sprintf(
                    '%d rows expected, %d found',
                    $numberOfRows,
                    count($rows)
                )
            );
        }

        for ($row = 0; $row < $numberOfRows; $row++) {
            $line = $rows[$row];

            if (strlen($line) !== $numberOfColumns) {
                throw new InvalidArgumentException(
                    sprintf(
                        '%d columns expected, %d found',
                        $numberOfColumns,
                        strlen($line)
                    )
                );
            }
            for ($column = 0; $column < $numberOfColumns; $column++) {
                $cell = $line[$column];
                if ($cell != '*' && $cell != '.') {
                    throw new InvalidArgumentException('Characters should be * or .');
                }
                $living                = $cell === '*' ? 1 : 0;
                $result[$row][$column] = $living;
            }
        }

        return $result;
    }

    /**
     * @param string $path
     * @return array
     */
    public static function fromFile(string $path)
    {
        if (!is_readable($path)) {
            throw new RuntimeException(sprintf('File "%s" is not readable', $path));
        }

        $grid = rtrim(file_get_contents($path));

        return self::fromText($grid);
    }
}
