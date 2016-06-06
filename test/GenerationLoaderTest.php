<?php

use CodingDojo\GenerationLoader;

final class GenerationLoaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_loads_a_generation_based_on_a_predefined_format()
    {
        $this->assertSame(
            [
                [0, 0, 0, 1, 0, 0],
                [0, 0, 1, 1, 0, 0],
                [0, 0, 0, 0, 0, 1],
                [0, 1, 0, 0, 0, 0],
            ],
            GenerationLoader::fromText(
                "6x4
...*..
..**..
.....*
.*...."
            )
        );
    }

    /**
     * @test
     */
    public function it_should_not_accept_letters_as_dimensions()
    {
        $this->setExpectedExceptionRegExp('InvalidArgumentException');
        GenerationLoader::fromText(
            "axb
...*..
..**..
.....*
.*...."
        );
    }

    /**
     * @test
     */
    public function it_should_not_accept_a_missing_rows_number()
    {
        $this->setExpectedExceptionRegExp('InvalidArgumentException');
        GenerationLoader::fromText(
            "8x
...*..
..**..
.....*
.*...."
        );
    }

    /**
     * @test
     */
    public function it_requires_a_line_describing_the_amount_of_columns_and_rows()
    {
        $this->setExpectedExceptionRegExp('InvalidArgumentException');
        GenerationLoader::fromText(
            "...*..
..**..
.....*
.*...."
        );
    }

    /**
     * @test
     * @dataProvider data_with_invalid_column_definitions
     */
    public function it_requires_the_correct_amount_of_columns_described_in_the_first_line($generation)
    {
        $this->setExpectedExceptionRegExp('InvalidArgumentException');

        GenerationLoader::fromText(
            $generation
        );
    }

    public function data_with_invalid_column_definitions()
    {
        return [
            'too few columns'  => [
                "1x4
...*..
..**..
.....*
.*....",
            ],
            'too many columns' => [
                "100x4
...*..
..**..
.....*
.*....",
            ],
        ];
    }

    /**
     * @test
     * @dataProvider data_with_invalid_rows_definitions
     */
    public function it_requires_the_correct_amount_of_rows_described_in_the_first_line($generation)
    {
        $this->setExpectedExceptionRegExp('InvalidArgumentException');

        GenerationLoader::fromText(
            $generation
        );
    }

    public function data_with_invalid_rows_definitions()
    {
        return [
            'too few rows'  => [
                "6x1
...*..
..**..
.....*
.*....",
            ],
            'too many rows' => [
                "6x100
...*..
..**..
.....*
.*....",
            ],
        ];
    }

    /**
     * @test
     **/

    public function rows_should_only_contain_stars_and_dots()
    {

        $this->setExpectedExceptionRegExp('InvalidArgumentException');

        GenerationLoader::fromText(
            "6x4
...@..
..@*..
.....*
.*...."
        );
    }

    /**
     * @test
     */
    public function it_loads_a_grid_from_a_file()
    {
        $this->assertSame(
            [
                [0, 0, 0],
                [1, 1, 1],
                [0, 0, 0],
            ],
            GenerationLoader::fromFile(__DIR__ . '/fixtures/grid.gol')
        );
    }

    /**
     * @test
     */
    public function it_will_throw_a_runtime_exception_when_the_file_is_not_readable()
    {
        $this->expectException(RuntimeException::class);
        GenerationLoader::fromFile(__DIR__ . '/fixtures/non-existent.gol');
    }
}
