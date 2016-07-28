<?php

namespace Kissge\ElasticaFriendlyResultSet\Test;

use Kissge\ElasticaFriendlyResultSet\Aggregations;
use Kissge\ElasticaFriendlyResultSet\Bucket;

/**
 * Functional tests.
 *
 * @author Yuto Kisuge <mail@yo.eki.do>
 */
class Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @group functional
     */
    public function testSimple()
    {
        $aggregations = new Aggregations([
            'interval' => [
                'buckets' => [
                    [
                        'key_as_string' => '2016-06-12T15:00:00.000Z',
                        'key' => 1465743600000,
                        'doc_count' => 100,
                        'averageScore' => [
                            'value' => 12345,
                        ],
                    ],
                    [
                        'key_as_string' => '2016-06-13T15:00:00.000Z',
                        'key' => 1465830000000,
                        'doc_count' => 200,
                        'averageScore' => [
                            'value' => 67890,
                        ],
                    ],
                ],
            ],
        ]);

        $keys = [];
        $count = [];
        $averageScore = [];

        foreach ($aggregations->interval as $key => $interval) {
            $keys[] = $key;
            $count[] = $interval->doc_count;
            $averageScore[] = $interval->averageScore;
        }

        $this->assertEquals($keys, [1465743600000, 1465830000000]);
        $this->assertEquals($count, [100, 200]);
        $this->assertEquals($averageScore, [12345, 67890]);
    }

    /**
     * @group functional
     */
    public function testNestedAggregations()
    {
        $aggregations = new Aggregations([
            'interval' => [
                'buckets' => [
                    [
                        'key_as_string' => '2016-06-12T15:00:00.000Z',
                        'key' => 1465743600000,
                        'doc_count' => 100,
                        'visitors' => [
                            'buckets' => [
                                'female' => [
                                    'doc_count' => 40,
                                ],
                                'male' => [
                                    'doc_count' => 50,
                                ],
                            ],
                        ],
                        'averageScore' => [
                            'value' => 12345,
                        ],
                    ],
                    [
                        'key_as_string' => '2016-06-13T15:00:00.000Z',
                        'key' => 1465830000000,
                        'doc_count' => 200,
                        'visitors' => [
                            'buckets' => [
                                'female' => [
                                    'doc_count' => 120,
                                ],
                                'male' => [
                                    'doc_count' => 60,
                                ],
                            ],
                        ],
                        'averageScore' => [
                            'value' => 67890,
                        ],
                    ],
                ],
            ],
        ]);

        $female = [];
        $male = [];
        $averageScore = [];

        foreach ($aggregations->interval as $interval) {
            $female[] = $interval->visitors->female;
            $male[] = $interval->visitors->male;
            $averageScore[] = $interval->averageScore;
        }

        $this->assertEquals($female, [40, 120]);
        $this->assertEquals($male, [50, 60]);
        $this->assertEquals($averageScore, [12345, 67890]);
    }

    /**
     * @group functional
     */
    public function testSubaggregations()
    {
        $aggregations = new Aggregations([
            'interval' => [
                'buckets' => [
                    [
                        'key_as_string' => '2016-05-31T15:00:00.000Z',
                        'key' => 1464706800000,
                        'doc_count' => 1000,
                        'editor' => [
                            'doc_count_error_upper_bound' => 0,
                            'sum_other_doc_count' => 0,
                            'buckets' => [
                                [
                                    'key' => 'Emacs',
                                    'doc_count' => 800,
                                ],
                                [
                                    'key' => 'nano',
                                    'doc_count' => 200,
                                ],
                            ],
                        ],
                    ],
                    [
                        'key_as_string' => '2016-06-30T15:00:00.000Z',
                        'key' => 1467298800000,
                        'doc_count' => 2000,
                        'editor' => [
                            'doc_count_error_upper_bound' => 0,
                            'sum_other_doc_count' => 0,
                            'buckets' => [
                                [
                                    'key' => 'SublimeText',
                                    'doc_count' => 1800,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $count = [];

        foreach ($aggregations->interval as $interval) {
            foreach ($interval->editor as $key => $editor) {
                $count[$key] = $editor->doc_count;
            }
        }

        $this->assertEquals($count, ['Emacs' => 800, 'nano' => 200, 'SublimeText' => 1800]);
    }

    /**
     * @expectedException \Exception
     * @group functional
     */
    public function testException()
    {
        (new Bucket([
            'key_as_string' => '2016-06-12T15:00:00.000Z',
            'key' => 1465743600000,
            'doc_count' => 100,
            'averageScore' => [
                'value' => 12345,
            ],
        ]))->nonExistent;
    }

    /**
     * @group functional
     */
    public function testCall()
    {
        $aggregations = new Aggregations([
            'interval' => [
                'buckets' => [
                    [
                        'key_as_string' => '2016-06-12T15:00:00.000Z',
                        'key' => 1465743600000,
                        'doc_count' => 100,
                        'averageScore' => [
                            'value' => 12345,
                        ],
                    ],
                    [
                        'key_as_string' => '2016-06-13T15:00:00.000Z',
                        'key' => 1465830000000,
                        'doc_count' => 200,
                        'averageScore' => [
                            'value' => 67890,
                        ],
                    ],
                ],
            ],
        ]);

        $averageScore = [];

        foreach ($aggregations->interval() as $key => $interval) {
            $averageScore[] = $interval->averageScore();
        }

        $this->assertEquals($averageScore, [12345, 67890]);
    }

    /**
     * @group functional
     */
    public function testArray()
    {
        $aggregations = new Aggregations([
            'interval' => [
                'buckets' => [
                    [
                        'key_as_string' => '2016-05-31T15:00:00.000Z',
                        'key' => 1464706800000,
                        'doc_count' => 1000,
                        'editor' => [
                            'doc_count_error_upper_bound' => 0,
                            'sum_other_doc_count' => 0,
                            'buckets' => [
                                [
                                    'key' => 'Emacs',
                                    'doc_count' => 800,
                                ],
                                [
                                    'key' => 'nano',
                                    'doc_count' => 200,
                                ],
                            ],
                        ],
                    ],
                    [
                        'key_as_string' => '2016-06-30T15:00:00.000Z',
                        'key' => 1467298800000,
                        'doc_count' => 2000,
                        'editor' => [
                            'doc_count_error_upper_bound' => 0,
                            'sum_other_doc_count' => 0,
                            'buckets' => [
                                [
                                    'key' => 'SublimeText',
                                    'doc_count' => 1800,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        foreach ($aggregations->interval as $interval) {
            $this->assertInternalType('array', $interval->editor);
        }
    }
}
