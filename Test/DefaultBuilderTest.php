<?php
/**
 * Created by Arsen.Ter-Oganisyan
 */

namespace Builder\Bundle\Ffc\Tests\QueryBuilder;

use Builder\Bundle\Ffc\QueryBuilder\Condition\CompositeConditionCollection;
use Builder\Bundle\Ffc\QueryBuilder\Condition\Condition;
use Builder\Bundle\Ffc\QueryBuilder\DefaultBuilder;


/**
 * Test for default builder
 */
class DefaultBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Builder class
     *
     * @var DefaultBuilder
     */
    protected $builder;

    /**
     * Test query structure
     *
     * @var array
     */
    protected $query = array(
        'conditions' => array(
            'operation' => 'and',
            'subList'   => array(
                array(
                    'condition' => array(
                        'type'   => 'IN',
                        'field'  => 'franchisee',
                        'values' => array(10, 53, 54)
                    )
                ),

                array(
                    'condition' => array(
                        'type'   => 'IN',
                        'field' => 'type',
                        'values' => array(1, 2, 3)
                    )
                ),

                array(
                    'operation' => 'or',
                    'subList'   => array(
                        array(
                            'condition' => array(
                                'type'   => 'IN',
                                'field' => 'id',
                                'values' => array(1, 2, 3)
                            )
                        ),

                        array(
                            'condition' => array(
                                'type'   => 'BETWEEN',
                                'field'  => 'srcAccount',
                                'values' => array(4, 5)
                            )
                        )
                    )
                ),
            )
        )
    );

    /**
     * Compiled condition
     *
     * @var string
     */
    protected $compiledCondition = "(t.franchisee IN (:franchisee1, :franchisee2, :franchisee3)) and (t.type IN (:type1, :type2, :type3)) and ((t.id IN (:id1, :id2, :id3)) or (t.srcAccount BETWEEN :srcAccount1 and :srcAccount2))";

    /**
     * Result binds
     *
     * @var array
     */
    protected $binds = array(
        'franchisee1' => 10,
        'franchisee2' => 53,
        'franchisee3' => 54,

        'type1' => 1,
        'type2' => 2,
        'type3' => 3,

        'id1' => 1,
        'id2' => 2,
        'id3' => 3,

        'srcAccount1' => 4,
        'srcAccount2' => 5,
    );

    /**
     * Set's up test
     *
     * @return void
     */
    public function setUp()
    {
        $this->builder = new DefaultBuilder(CompositeConditionCollection::class, Condition::class);
    }

    /**
     * Test getDql method
     */
    public function testGetDql()
    {
        $this->assertEquals(
            $this->builder
                 ->getDql($this->query),

            $this->compiledCondition
        );
    }

    /**
     * Test getBinds method
     */
    public function testGetBinds()
    {
        $this->assertEquals(
            $this->builder
                 ->getBinds($this->query),

            $this->binds
        );
    }
}
