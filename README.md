# DeepCopy

DeepCopy helps you create deep copies (clones) of your objects. It is designed to handle cycles in the association graph.

[![Build Status](https://travis-ci.org/myclabs/DeepCopy.png?branch=master)](https://travis-ci.org/myclabs/DeepCopy)
[![Coverage Status](https://coveralls.io/repos/myclabs/DeepCopy/badge.png?branch=master)](https://coveralls.io/r/myclabs/DeepCopy?branch=master)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/myclabs/DeepCopy/badges/quality-score.png?s=2747100c19b275f93a777e3297c6c12d1b68b934)](https://scrutinizer-ci.com/g/myclabs/DeepCopy/)
[![Total Downloads](https://poser.pugx.org/myclabs/deep-copy/downloads.svg)](https://packagist.org/packages/myclabs/deep-copy)


## Table of Contents

1. [How](#how)
1. [Why](#why)
    1. [Using simply `clone`](#using-simply-clone)
    1. [Overridding `__clone()`](#overridding-__clone)
    1. [With `DeepCopy`](#with-deepcopy)
1. [How it works](#how-it-works)
1. [Going further](#going-further)
    1. [Matchers](#matchers)
        1. [Property name](#property-name)
        1. [Specific property](#specific-property)
        1. [Type](#type)
    1. [Filters](#filters)
        1. [`SetNullFilter`](#setnullfilter-filter)
        1. [`KeepFilter`](#keepfilter-filter)
        1. [`DoctrineCollectionFilter`](#doctrinecollectionfilter-filter)
        1. [`DoctrineEmptyCollectionFilter`](#doctrineemptycollectionfilter-filter)
        1. [`DoctrineProxyFilter`](#doctrineproxyfilter-filter)
        1. [`ReplaceFilter`](#replacefilter-type-filter)
        1. [`ShallowCopyFilter`](#doctrinecollectionfilter-type-filter)
1. [Edge cases](#edge-cases)
1. [Contributing](#contributing)
    1. [Tests](#tests)


## How?

Install with Composer:

```json
composer require myclabs/deep-copy
```

Use simply:

```php
use Belime\DeepCopyLegacy\DeepCopy;

$copier = new DeepCopy();
$myCopy = $copier->copy($myObject);
```


## Why?

- How do you create copies of your objects?

```php
$myCopy = clone $myObject;
```

- How do you create **deep** copies of your objects (i.e. copying also all the objects referenced in the properties)?

You use [`__clone()`](http://www.php.net/manual/en/language.oop5.cloning.php#object.clone) and implement the behavior
yourself.

- But how do you handle **cycles** in the association graph?

Now you're in for a big mess :(

![association graph](doc/graph.png)


### Using simply `clone`

![Using clone](doc/clone.png)


### Overridding `__clone()`

![Overridding __clone](doc/deep-clone.png)


### With `DeepCopy`

![With DeepCopy](doc/deep-copy.png)


## How it works

DeepCopy recursively traverses all the object's properties and clones them. To avoid cloning the same object twice it
keeps a hash map of all instances and thus preserves the object graph.

To use it:

```php
use function DeepCopy\deep_copy;

$copy = deep_copy($var);
```

Alternatively, you can create your own `DeepCopy` instance to configure it differently for example:

```php
use Belime\DeepCopyLegacy\DeepCopy;

$copier = new DeepCopy(true);

$copy = $copier->copy($var);
```

You may want to roll your own deep copy function:

```php
namespace Acme;

use Belime\DeepCopyLegacy\DeepCopy;

function deep_copy($var)
{
    static $copier = null;
    
    if (null === $copier) {
        $copier = new DeepCopy(true);
    }
    
    return $copier->copy($var);
}
```


## Going further

You can add filters to customize the copy process.

The method to add a filter is `DeepCopy\DeepCopy::addFilter($filter, $matcher)`,
with `$filter` implementing `DeepCopy\Filter\Filter`
and `$matcher` implementing `DeepCopy\Matcher\Matcher`.

We provide some generic filters and matchers.


### Matchers

  - `DeepCopy\Matcher` applies on a object attribute.
  - `DeepCopy\TypeMatcher` applies on any element found in graph, including array elements.


#### Property name

The `PropertyNameMatcher` will match a property by its name:

```php
use Belime\DeepCopyLegacy\Matcher\PropertyNameMatcher;

// Will apply a filter to any property of any objects named "id"
$matcher = new PropertyNameMatcher('id');
```


#### Specific property

The `PropertyMatcher` will match a specific property of a specific class:

```php
use Belime\DeepCopyLegacy\Matcher\PropertyMatcher;

// Will apply a filter to the property "id" of any objects of the class "MyClass"
$matcher = new PropertyMatcher('MyClass', 'id');
```


#### Type

The `TypeMatcher` will match any element by its type (instance of a class or any value that could be parameter of
[gettype()](http://php.net/manual/en/function.gettype.php) function):

```php
use Belime\DeepCopyLegacy\TypeMatcher\TypeMatcher;

// Will apply a filter to any object that is an instance of Doctrine\Common\Collections\Collection
$matcher = new TypeMatcher('Doctrine\Common\Collections\Collection');
```


### Filters

- `DeepCopy\Filter` applies a transformation to the object attribute matched by `DeepCopy\Matcher`
- `DeepCopy\TypeFilter` applies a transformation to any element matched by `DeepCopy\TypeMatcher`


#### `SetNullFilter` (filter)

Let's say for example that you are copying a database record (or a Doctrine entity), so you want the copy not to have
any ID:

```php
use Belime\DeepCopyLegacy\DeepCopy;
use Belime\DeepCopyLegacy\Filter\SetNullFilter;
use Belime\DeepCopyLegacy\Matcher\PropertyNameMatcher;

$object = MyClass::load(123);
echo $object->id; // 123

$copier = new DeepCopy();
$copier->addFilter(new SetNullFilter(), new PropertyNameMatcher('id'));

$copy = $copier->copy($object);

echo $copy->id; // null
```


#### `KeepFilter` (filter)

If you want a property to remain untouched (for example, an association to an object):

```php
use Belime\DeepCopyLegacy\DeepCopy;
use Belime\DeepCopyLegacy\Filter\KeepFilter;
use Belime\DeepCopyLegacy\Matcher\PropertyMatcher;

$copier = new DeepCopy();
$copier->addFilter(new KeepFilter(), new PropertyMatcher('MyClass', 'category'));

$copy = $copier->copy($object);
// $copy->category has not been touched
```


#### `DoctrineCollectionFilter` (filter)

If you use Doctrine and want to copy an entity, you will need to use the `DoctrineCollectionFilter`:

```php
use Belime\DeepCopyLegacy\DeepCopy;
use Belime\DeepCopyLegacy\Filter\Doctrine\DoctrineCollectionFilter;
use Belime\DeepCopyLegacy\Matcher\PropertyTypeMatcher;

$copier = new DeepCopy();
$copier->addFilter(new DoctrineCollectionFilter(), new PropertyTypeMatcher('Doctrine\Common\Collections\Collection'));

$copy = $copier->copy($object);
```


#### `DoctrineEmptyCollectionFilter` (filter)

If you use Doctrine and want to copy an entity who contains a `Collection` that you want to be reset, you can use the
`DoctrineEmptyCollectionFilter`

```php
use Belime\DeepCopyLegacy\DeepCopy;
use Belime\DeepCopyLegacy\Filter\Doctrine\DoctrineEmptyCollectionFilter;
use Belime\DeepCopyLegacy\Matcher\PropertyMatcher;

$copier = new DeepCopy();
$copier->addFilter(new DoctrineEmptyCollectionFilter(), new PropertyMatcher('MyClass', 'myProperty'));

$copy = $copier->copy($object);

// $copy->myProperty will return an empty collection
```


#### `DoctrineProxyFilter` (filter)

If you use Doctrine and use cloning on lazy loaded entities, you might encounter errors mentioning missing fields on a
Doctrine proxy class (...\\\_\_CG\_\_\Proxy).
You can use the `DoctrineProxyFilter` to load the actual entity behind the Doctrine proxy class.
**Make sure, though, to put this as one of your very first filters in the filter chain so that the entity is loaded
before other filters are applied!**

```php
use Belime\DeepCopyLegacy\DeepCopy;
use Belime\DeepCopyLegacy\Filter\Doctrine\DoctrineProxyFilter;
use Belime\DeepCopyLegacy\Matcher\Doctrine\DoctrineProxyMatcher;

$copier = new DeepCopy();
$copier->addFilter(new DoctrineProxyFilter(), new DoctrineProxyMatcher());

$copy = $copier->copy($object);

// $copy should now contain a clone of all entities, including those that were not yet fully loaded.
```


#### `ReplaceFilter` (type filter)

1. If you want to replace the value of a property:

```php
use Belime\DeepCopyLegacy\DeepCopy;
use Belime\DeepCopyLegacy\Filter\ReplaceFilter;
use Belime\DeepCopyLegacy\Matcher\PropertyMatcher;

$copier = new DeepCopy();
$callback = function ($currentValue) {
  return $currentValue . ' (copy)'
};
$copier->addFilter(new ReplaceFilter($callback), new PropertyMatcher('MyClass', 'title'));

$copy = $copier->copy($object);

// $copy->title will contain the data returned by the callback, e.g. 'The title (copy)'
```

2. If you want to replace whole element:

```php
use Belime\DeepCopyLegacy\DeepCopy;
use Belime\DeepCopyLegacy\TypeFilter\ReplaceFilter;
use Belime\DeepCopyLegacy\TypeMatcher\TypeMatcher;

$copier = new DeepCopy();
$callback = function (MyClass $myClass) {
  return get_class($myClass);
};
$copier->addTypeFilter(new ReplaceFilter($callback), new TypeMatcher('MyClass'));

$copy = $copier->copy([new MyClass, 'some string', new MyClass]);

// $copy will contain ['MyClass', 'some string', 'MyClass']
```


The `$callback` parameter of the `ReplaceFilter` constructor accepts any PHP callable.


#### `ShallowCopyFilter` (type filter)

Stop *DeepCopy* from recursively copying element, using standard `clone` instead:

```php
use Belime\DeepCopyLegacy\DeepCopy;
use Belime\DeepCopyLegacy\TypeFilter\ShallowCopyFilter;
use Belime\DeepCopyLegacy\TypeMatcher\TypeMatcher;
use Mockery as m;

$this->deepCopy = new DeepCopy();
$this->deepCopy->addTypeFilter(
	new ShallowCopyFilter,
	new TypeMatcher(m\MockInterface::class)
);

$myServiceWithMocks = new MyService(m::mock(MyDependency1::class), m::mock(MyDependency2::class));
// All mocks will be just cloned, not deep copied
```


## Edge cases

The following structures cannot be deep-copied with PHP Reflection. As a result they are shallow cloned and filters are
not applied. There is two ways for you to handle them:

- Implement your own `__clone()` method
- Use a filter with a type matcher


## Contributing

DeepCopy is distributed under the MIT license.


### Tests

Running the tests is simple:

```php
vendor/bin/phpunit
```
