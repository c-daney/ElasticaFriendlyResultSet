ElasticaFriendlyResultSet
=========================

[![Build Status](https://travis-ci.org/kissge/ElasticaFriendlyResultSet.svg?branch=master)](https://travis-ci.org/kissge/ElasticaFriendlyResultSet)
[![Coverage Status](https://coveralls.io/repos/github/kissge/ElasticaFriendlyResultSet/badge.svg?branch=master)](https://coveralls.io/github/kissge/ElasticaFriendlyResultSet?branch=master)

Thin but powerful wrapper for [ruflin/Elastica](https://github.com/ruflin/Elastica).

Installation
------------

Simply add this library to your project as a dependency:

```bash
composer require kissge/elastica-friendly-result-set dev-master
```

Usage
-----

```php
use Kissge\ElasticaFriendlyResultSet\Aggregations;

$index = $container->get('fos_elastica.index.<index name>');
$aggs = new Aggregations($index->search($query)->getAggregations());

foreach ($aggs-><aggregation name> as $key => $bucket) {
    do_something($bucket-><subaggregation name>);
    do_something($bucket-><subaggregation name>-><term name>);
}
```

Author
------

[Yuto Kisuge](https://github.com/kissge)

License
-------

This library is licensed under the MIT License. See `LICENSE` for the complete license.
