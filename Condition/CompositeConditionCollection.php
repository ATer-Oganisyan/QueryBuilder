<?php
/**
 * Created by Arsen Ter.Oganisyan
 */

namespace Builder\Bundle\Ffc\QueryBuilder\Condition;

/**
 * Composite query builder
 */
class CompositeConditionCollection implements ConditionListInterface
{

    /**
     * Conditions operation
     *
     * @var string
     */
    private $operation;

    /**
     * Collection of sub collection
     *
     * @var array
     */
    private $subCollection = array();

    /**
     * Constructor
     *
     * @param string $operation operation
     */
    public function __construct($operation)
    {
        $this->operation = $operation;
    }

    /**
     * {@inheritdoc}
     */
    public function add(ConditionListInterface $cl)
    {
        $this->subCollection[] = $cl;
    }

    /**
     * {@inheritdoc}
     */
    public function getConditionString()
    {
        $string = array();
        foreach ($this->subCollection as $item) {
            $string[] = "({$item->getConditionString()})";
        }
        return implode(" {$this->operation} ", $string);
    }
}
