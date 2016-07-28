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
     * [Magic method] Returns an array of bucket objects.
     *
     * @param string $name
     * @return array
     */
    public function __get($name)
    {
        return Bucket::convertToBucketArray($this->raw[$name]['buckets']);
    }

    /**
     * [Magic method] Returns an array of bucket objects.
     *
     * @param string $name
     * @param array  $arguments
     * @return array
     */
    public function __call($name, array $arguments)
    {
        return $this->__get($name);
    }
}
