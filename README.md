*Danek\Validator* is a very small validation library, with the easiest and most usable API we could possibly create.

## Install
To easily include *Danek\Validator* into your project, install it via [composer](https://getcomposer.org) using the command line:

```bash
composer require danek/validator
```

## Small usage example

```php
use Danek\Validator\Validator;

$v = new Validator;

$v->required('user.first_name')->lengthBetween(2, 50)->alpha();
$v->required('user.last_name')->lengthBetween(2, 50)->alpha();
$v->required('newsletter')->bool();

$result = $v->validate([
    'user' => [
        'first_name' => 'John',
        'last_name' => 'D',
    ],
    'newsletter' => true,
]);

$result->isValid(); // bool(false).
$result->getMessages();
/**
 * array(1) {
 *     ["user.last_name"]=> array(1) {
 *         ["Length::TOO_SHORT"]=> string(53) "last_name is too short and must be 2 characters long."
 *     }
 * }
 */
```

## Functional features

* Validate an array of data
* Get an array of error messages
* Overwrite the default error messages on rules, or error messages on specific values
* Get the validated values of an array
* Validate different contexts (insert, update, etc.) inheriting validations of the default context
* Ability to extend the validator to add your own custom rules

## Non functional features

* Easy to write (IDE auto-completion for easy development)
* Easy to read (improves peer review)
* Ability to separate controller and view logic
* Zero dependencies