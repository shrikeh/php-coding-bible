# PHP Coding Bible
Bible of how we want to write maintainable code and applications.

## Ethos

While the [Zen of Python][zen-of-python] is, well, about Python, our code can also benefit from the wisdom of Tim Peters:

* Beautiful is better than ugly.
* Explicit is better than implicit.
* Simple is better than complex. 
* Complex is better than complicated. 
* Flat is better than nested. 
* Sparse is better than dense. 
* Readability counts. 
* Special cases aren't special enough to break the rules. 
* Although practicality beats purity. 
* Errors should never pass silently. 
* Unless explicitly silenced. 
* In the face of ambiguity, refuse the temptation to guess. 
* There should be one-- and preferably only one --obvious way to do it. 
* Although that way may not be obvious at first unless you're Dutch. 
* Now is better than never. 
* Although never is often better than *right* now. 
* If the implementation is hard to explain, it's a bad idea. 
* If the implementation is easy to explain, it may be a good idea. 
* Namespaces are one honking great idea -- let's do more of those!

---

## Overarching Concepts

_<h1 style="text-align: center;">Don't. Ship. Shit.</h1>_

> "You don't guess that your code works; You know that it works"
> <div style="text-align: right">- Uncle Bob</div>

[Here's the talk][uncle-bob]. Required viewing.

_Code without tests is not production-ready_: it's as simple as that. Your tests should mean you know it works before it's deployed. That may mean refactoring code, but we are professionals and the job isn't supposed to be easy.

### The Low-Level stuff

Write your code to be:
* [Clean!][clean-code]
* [PSR-12][psr-12] compliant
* [SOLID][solid]
* Testable
* Maintainable

### Tests are first-class citizens in your application

Think about it. They're the reason you don't have to write so much documentation, how you know you aren't breaking somebody else's code (and that they can't break yours). They need to be fast and light, atomic enough they can run in parallel, and be easily maintained. So yes, they are equally as valuable as the application code in knowing you're done. Treat them with the same care you would for application code (which is why they should get linted and quality checked as well).

### Classes that are easy to test are easy to use and maintain

A core advantage of TDD isn't about tests but how writing code to pass tests makes you keen to easily setup the *S*ubject *U*nder *T*est, and that the SUT has a limited number of methods and permutations that require testing.

### Favour Composition over Inheritance

There should be a *very* good reason to extend a class. Inheritance causes all sorts of problems with tests, overriding methods, etc. Make your classes `final` and use collaborators.

### Classes Should Self-Validate On Construction

It should be impossible for an object to be created in an invalid state. Objects should self-validate upon construction to ensure they are meaningful and to capture early problems.

```php
# Bad
class Refund
{
    public function __construct(private float $amount)
    {}
}

# Good
final class Refund
{
    public function __construct(private float $amount)
    {
        if (0 >= $amount) {
            // Refunds must be above zero.
            throw RefundNotPositiveException::create($amount);
        }
    }
}
```

### A constructor shouldn't have to normalize data, just validate it

In the case where an object could be created from multiple types of data, it's better to make the `__construct()` method private and instead use _Static Named Constructors_. The static methods can then normalize the data, while the true constructor validates it:

```php
final class PdfPath
{
    public static function fromString(string $path): self
    {
        return new self (new SplFileObject($path));
    }
    
    public static function fromFileInfo(SplFileInfo $fileInfo): self
    {
        return new self($fileInfo->openFile());
    }
    
    private function __construct(private SplFileObject $pdfPath)
    {
        if (!$this->pdfPath->isReadable()) {
        // ...
        }
    }
}
```

### The "Rule" Of Three

A method, including a constructor, should have a maximum of three arguments to it.

### Collaboration is Key

_Collaborators_, be they _aggregates_ or providers of functionality, provide the building blocks of good code. They allow the above "rule" to be more easily followed, aid in testing, maintainability, and reuse.

A simple Aggregate example for use within a `Customer`:

```php
namespace Shrikeh\Customer;

final class ContactDetails
{
    public function __construct(
        private Address $address, 
        private EmailAddress $emailAddress,
        private TelephoneNumber $telephoneNumber,
    ) {
    
    }
    
    // ... getters
}
```

Because the above class is using _Value Objects_, which self-validate themselves, the aggregate needs no validation itself. This then could be used as an argument to a `Customer` constructor.

But how should you access these aggregated details? Well...

### Practice the Law of Demeter

> _"When one wants a dog to walk, one does not command the dog's legs to walk directly; instead one commands the dog which then commands its own legs."_
> <div style="text-align: right">- Wikipedia</div>

The _[Law of Demeter][law-of-demeter]_ is a principle of least knowledge; that is, the properties and structure of an object should not be replied upon by something using it. Instead, the object should have a method that does the work for the external user:

```php
# Bad
$address = $customer->getProfile()->getHomeAddress();

# Good
$address = $customer->getHomeAddress();
```

### Failure should be exceptional

Classes should do what is expected of them or throw a specific Exception. Don't return `true`; it is expected to work!

Remember: if you are relying on a collaborator, and it throws an Exception to use the previous Exception:

```php
final class DbalCustomerRegistryRepository
{
//...
    public function registerCustomer(Customer $customer): void
    {
        try {
            $this->client->save($customer->getName());
        } catch (ClientException $exc) {
            throw UnableToRegisterCustomerException::create($customer, $exc);
        }
    }
}
```
Concerned the above doesn't return an ID? Why do you need one, your application has already specified the UID ahead of time via the use of UUIDv5 or UUIDv6.

### Comments are a code smell

Think about it: why is your code so complex you need to explain it? Strong variable names and typing, combined with equally strong method names and brevity, should not require War & Peace to describe them. Functional testing with Gherkin, combined with unit testing obeying the principle of Specification By Example, should provide enough information.

### Set is Evil
Objects should be immutable, and created in a specific state which they carry for their lifetime.
If you must use `set`, it should return a new instance with the new value and leave the original unchanged:

```php
final class Foo
{
    public function setBar(string $bar): self
    {
        return new self($bar);
    }
}
```

### Get is Evil
Or at least, getters are not necessary for simply returning property values from PHP 8.1, following the addition of `public readonly` properties:

```php
final class Foo
{
    public function __construct(
        public readonly string $bar
    ) {
    }
}

$foo = new Foo('sample string');
echo $foo->bar; // sample string
```

replaces

```php
final class Foo
{
    private string $bar;
    
    public function __construct(
        string $bar
    ) {
        $this->bar = $bar;
    }
    
    public function getBar(): string
    {
        return $this->bar;
    }
}

$foo = new Foo('sample string');
echo $foo->getBar(); // sample string
```

#### Why?

There are two main reasons to follow the `public readonly` approach:
- Brevity - The first code snippet above is much shorter than the second. It's fewer lines of code to write, and more readable too.
- Saves on unit testing - In a project with a minimum code coverage expectation, it would be necessary to unit test all methods including getters. Whilst this is a trivial test to write, no test is required with the `public readonly` approach.

### New is Evil
There are only a few things that should create other things:

- An object should be able to create a new instance of itself (ie to follow the practice of immutability above)
- A dedicated factory, ideally meeting an interface.
- Dependency Injection containers

Let's imagine a database repository matching the following contract:
```php
interface CustomerDetailsRepository
{
    public function fetchCustomerDetails(CustomerId $customerId): CustomerDetails;
}
```
Now, an implementation that essentially handles the query and Exception handling, without having to know how to create the `CustomerDetails`:

```php

final class DBCustomerDetailsRepository implements CustomerDetailsRepository
{
    public function __construct(private Client $client, private CustomerDetailsFactory $customerDetailsFactory)
    {
        
    }

    public function fetchCustomerDetails(CustomerId $customerId): CustomerDetails
    {
        try {
            $row = $this->queryCustomerDetails($customerId);
            
            return $this->customerDetailsFactory->build($row);
        } catch (SomeClientException $exc) {
            // ... throw a Repository exception
        }
    }

    private function queryCustomerDetails(): SomeDbRow
    {
        //...
    }
}
```

The above is going to be _far_ easier to mock, test, and maintain.

### Make variables and methods private by default

You should only make the minimum number of methods public to fulfill the contract of external use.

### Prefer class constants over magic numbers

You know how it is. Months after you wrote a wonderful algorithm that is blisteringly fast and your peers adore you for, you now have to go back into it with a new team member.

```php
final class AwesomeAlgorithm
{
    public function calc(float $marketVolatility, float $weight): float
    {
        return (2.17 ** $marketVolatility) - (1 - $weight);
    }
}
```
Why is it 2.17, they ask innocently? You panic, not remembering what this magic number is. You see the future all too clearly now: how they will see through your facade of professionalism, how your team will mock you, your family turn their backs on you, and LinkedIn will close your profile. As the sweat pours down your forehead, desperately trying to remember what any of the calculation means, you can tell it is already too late as the first drizzle of perspiration crashes against the keys of your keyboard, and you smell the burning of electronics.

If only you had written it like this:
```php
final class AwesomeAlgorithm
{
    private const LONG_TERM_VOLATILITY_YIELD = 2.17;

    public function calc(float $marketVolatility, float $weight): float
    {
        return (self::LONG_TERM_VOLATILITY_YIELD ** $marketVolatility) - (1 - $weight);
    }
}
```

### Prefer Enums over multiple class constants of the same "thing"

Having replaced both your predecessor and their moisture-damaged keyboard, you resolve to not make the same mistakes.

Here's a (real) extract from a class written before PHP had enums:

```php
class HsSomeBankCashTx extends DumbActiveRecord
{
    //...

    public const TLA_CODE_TRANSFER = 'TFR';
    public const TLA_CODE_DIRECT_DEBIT = 'DDR';
    public const TLA_BILL_PAYMENT = 'BBP';
    public const TLA_CODE_BANK_GIRO_CREDIT = 'BGC';
    public const TLA_CODE_FASTER_PAYMENT_CREDIT = 'FPC';
    public const TLA_CODE_FASTER_PAYMENT_DEBIT = 'FP';
    public const TLA_CREDIT = 'CR';
    public const TLA_CODE_FASTER_PAYMENT_TRANSFER = 'FT';
    public const TLA_STANDING_ORDER = 'STO';
    //...

```

You keep your cool, and you refactor the class by extracting these constants into an [Enumerable][enums]:

```php
enum SomeBankTlaCode: string
{
    case Transfer = 'TFR';
    case DirectDebit = 'DDR';
    case BillPayment = 'BBP';
    case BankGiroCredit = 'BGC';
    case FasterPaymentCredit = 'FPC';
    case FasterPaymentDebit = 'FP';
    case Credit = 'CR';
    case FasterPaymentTransfer = 'FT';
    case StandingOrder = 'STO';
}
```

### Enforce strict typing with [`declare(strict_types=1)`](https://www.php.net/manual/en/language.types.declarations.php#language.types.declarations.strict)

Newer versions of PHP allows `scalar` types declarations.

Unfortunately these types can still be coerced into wrong types, take as an example:

```php
<?php

function myFunc(string $str): void 
{	
	echo $str;	
}

myFunc(123);
```
See [https://3v4l.org/6YfXoK]()

This will simply output `123`

Now we add `declare(strict_types=1);`

```php
<?php

declare(strict_types=1);

function myFunc(string $str): void 
{	
	echo $str;	
}

myFunc(123); 
```
See [https://3v4l.org/smJf1]()

And we now get:
```
Fatal error: Uncaught TypeError: myFunc(): Argument #1 ($str) must be of type string, int given
```
in php >= 8 and for php 7 we get:
```
Fatal error: Uncaught TypeError: Argument 1 passed to myFunc() must be of the type string, int given
```

So practice safe coding and always use `declare(strict_types=1);`

[zen-of-python]: https://peps.python.org/pep-0020/
[uncle-bob]: https://youtu.be/LmRl0D-RkPU?t=2091
[psr-12]: https://www.php-fig.org/psr/psr-12/
[solid]: https://www.digitalocean.com/community/conceptual_articles/s-o-l-i-d-the-first-five-principles-of-object-oriented-design
[clean-code]: https://github.com/jupeter/clean-code-php
[law-of-demeter]: https://en.wikipedia.org/wiki/Law_of_Demeter
[enums]: https://www.php.net/manual/en/language.enumerations.overview.php
