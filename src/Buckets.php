<?php

namespace Kissge\ElasticaFriendlyResultSet;

/**
 * ResultSet wrapper class for buckets.
 *
 * @author Yuto Kisuge <mail@yo.eki.do>
 */
class Buckets implements \IteratorAggregate
{
    /** @var array */
    private $raw;

    /**
     * Creates a new buckets object.
     *
     * @param array $raw
     */
    public function __construct(array $raw)
    {
        $this->raw = $raw;
    }

    /**
     * Iterates through the buckets, and yields every bucket object.
     * {@inheritDoc}
     */
    public function getIterator()
    {
        foreach ($this->raw as $bucket) {
            yield $bucket['key'] => new Bucket($bucket);
        }
    }
}
