<?php

namespace Kissge\ElasticaFriendlyResultSet;

/**
 * ResultSet wrapper class for a bucket.
 *
 * @author Yuto Kisuge <mail@yo.eki.do>
 */
class Bucket
{
    /** @var array */
    private $raw;

    /** @var string */
    private $key;

    /** @var string|null */
    private $key_as_string;

    /** @var int */
    private $doc_count;

    /**
     * Creates a new bucket object.
     *
     * @param array $raw
     */
    public function __construct(array $raw)
    {
        $this->raw = $raw;

        foreach (['key', 'key_as_string', 'doc_count'] as $key) {
            $this->{$key} = @$raw[$key];
        }
    }

    /**
     * [Magic method] Returns something.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            // Key and document count are obtained directly
            return $this->{$name};
        } elseif (isset($this->raw[$name])) {
            if (isset($this->raw[$name]['buckets'])) {
                // This bucket holds further inner buckets
                if (self::isAssociativeArray($this->raw[$name]['buckets'])) {
                    // e.g. (generally multiple) filters
                    return (object) array_map(function ($arr) {
                        return $arr['doc_count'];
                    }, $this->raw[$name]['buckets']);
                } else {
                    // e.g. subaggregations
                    return self::convertToBucketArray($this->raw[$name]['buckets']);
                }
            } else {
                // e.g. sum of a field
                return $this->raw[$name]['value'];
            }
        } else {
            throw new \Exception("Undefined property $name.");
        }
    }

    /**
     * [Magic method] Returns something.
     *
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        return $this->__get($name);
    }

    /**
     * Converts a sequential array into an associative array of bucket objects.
     *
     * @param array $buckets
     * @static
     * @return array
     */
    public static function convertToBucketArray(array $buckets)
    {
        return array_combine(
            array_map(function ($elem) {
                return $elem['key'];
            }, $buckets),
            array_map(function ($elem) {
                return new Bucket($elem);
            }, $buckets)
        );
    }

    /**
     * Checks whether an array is associative.
     *
     * @param array $arr
     * @static
     * @return bool
     */
    private static function isAssociativeArray(array $arr)
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
