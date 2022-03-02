# Supermarket checkout - Programming exercise
Written in PHP with Laravel by William Icke

You can find the full brief for this in the `Docs` directory.

## How to run
You must have PHP and Composer installed

Open a terminal in the root directory of the project and run the commands below:  
`composer install` - This installs any and all dependencies

`php artisan serve` - This will run the project, which can be accessed from localhost:8000

## Thoughts into decisions and processes
- Firstly I chose to use Laravel as it's the framework I was most familiar with, and using a framework would allow me to get straight into developing functionality
- Ideally, if this was a real-world project and if I had more time, I would refactor the project into a proper MVC structure. However, to save time I have implemented most of the backend code inside Checkout controller.
    - With more time I would look at creating Models for SKU units, full with function to fetch all the necessary attributes
    - I would also implement an abstract model for Special Offers, and have two models that extend from that for the 'Purchase with' and 'Quantity based' offers. This would remove the need to detect which type of offer is present before proceeding.
- The SKU data is also currently held as a constant in the controller, for the sake of simplicity. Ideally, this would be stored in a database but that is out of scope. While this is simpler and much easier, there are drawbacks, specifically concerning unit testing as we cannot fully control unit test data. This affects what I can unit test, but I've added what I can for now.
- I have implemented basic unit tests, with more time I would refactor these to be cleaner and easier to read/use
- The tests implemented including some of the examples from the brief along with some general cases.
- While the SKU data is fixed as a constant, the calculation of the checkout is not. It will work with whatever SKU data is presented to it, so feel free to alter the constant in the controller
    - PLEASE NOTE: Altering this constant will cause the unit tests to break, as they will rely on this constant
    - When calculating an item with multiple special offers it will use recursion where necessary, this is so the lowest total cost can be found across multiple special offers
- Assumptions made:
    - One assumption made is that special offers are always better (lowest cost) than the individual unit price
    - Another is that all prices are integers, no decimals (floats). In a real world scenario this would use floats, but for simplicity this project uses integers