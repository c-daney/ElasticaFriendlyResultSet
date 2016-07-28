<?php

namespace Kissge\ElasticaFriendlyResultSet;

/**
 * ResultSet wrapper class for aggregations.
 *
 * @author Yuto Kisuge <mail@yo.eki.do>
 */
class Aggregations
{
    /** @var array */
    private $raw;

    /**
     * Creates a new aggregations object.
     *
     * @param array $raw
     */
    public function __construct(array $raw)
    {
        $this->raw = $raw;
    }

    /**
     * [Magic method] Returns a buckets object.
     *
     * @param string $name
     * @return Buckets
     */
    public function __get($name)
    {
        return new Buckets($this->raw[$name]['buckets']);
    }

    /**
     * [Magic method] Returns a buckets object.
     *
     * @param string $name
     * @param array  $arguments
     * @return Buckets
     */
    public function __call($name, array $arguments)
    {
        return $this->__get($name);
    }
}
