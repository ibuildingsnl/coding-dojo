# GameOfLife

## Description of Conway's Game Of Life

*Source: [LifeWiki](http://www.conwaylife.com/wiki/Conway's_Game_of_Life)*

The universe of the Game of Life is an infinite two-dimensional orthogonal grid of square cells, each of which is in one of two possible states, live or dead. Every cell interacts with its eight neighbours, which are the cells that are directly horizontally, vertically, or diagonally adjacent. At each step in time, the following transitions occur:

- Any live cell with fewer than two live neighbours dies (referred to as underpopulation).
- Any live cell with more than three live neighbours dies (referred to as overpopulation or overcrowding).
- Any live cell with two or three live neighbours lives, unchanged, to the next generation.
- Any dead cell with exactly three live neighbours will come to life.

The initial pattern constitutes the 'seed' of the system. The first generation is created by applying the above rules simultaneously to every cell in the seed — births and deaths happen simultaneously, and the discrete moment at which this happens is sometimes called a tick. (In other words, each generation is a pure function of the one before.) The rules continue to be applied repeatedly to create further generations.

## Description of the kata

1. Provide an implementation for a grid as described above. You should be able to provide a set of coordinates for the *living cells*. The grid should be able to calculate the next generation grid.
2. Provide an implementation for a text loader which creates a grid object based on a notation for living cells, like:

> 6x4
> ...*..
> ..**..
> .....*
> .*....

3. Provide an implementation of a file loader, which loads a file containing a text-based pattern like the one described above and creates a grid object based on it.
4. Provide an implementation of a grid printer, which prints grids and their next generation to the terminal.
5. Combine all of the above in a command-line based application which loads the text file provided as the first argument and keeps printing new generations every `n` seconds.
