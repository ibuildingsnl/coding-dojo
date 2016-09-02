# Code Burn: The Page Class

## Description

The `Page` class is a very early, uninformed try at introducing OOP in a very old "CMS". It is a complete mess and violates every programming principle we know now.

## Improving the code

Steps to improve the `Page` class might be:

- Move `define`d configuration values into a dedicated `Configuration` object.
- Apply the Dependency Inversion Principle on external dependencies, like the templating engine `Smarty` and the `mysql_*` functions.
- Instead of directly sending output and headers, use output buffering (using the `ob_*` functions) and collect headers before sending them to the HTTP client.
- Use meaningful objects instead of "anonymous" arrays.
- Move the `sfTimer` stuff to a decorator.
- Reduce the amount of public functions by moving "static" functionality to some other object.
- Enhance the level encapsulation of this object by making attributes private and making sure that a `Page` object will always behave consistently from object client's point of view.
