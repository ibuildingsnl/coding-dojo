# WordWrap

You write a class called `Wrapper`, that has a single static function named `wrap` that takes two arguments, a string, and a column number. The function returns the string, but with line breaks inserted at just the right places to make sure that no line is longer than the column number. You try to break lines at word boundaries.

Some examples (when the column width is 3):

    kopje => kop\nje

    kopje koffie => kop\nje\nkof\nfie

## Suggestions

- If you end up writing a large part of the algorithm at once, instead of taking small steps toward the solution, take a step back and think of simpler or different test cases to get the mind unstuck.
- Think of as many edge cases as possible and cover them with a test (a space at the column width, before or after, an empty string, etc.).
- Once you have recursion, consider implementing the function using [tail recursion](http://c2.com/cgi/wiki?TailRecursion).
