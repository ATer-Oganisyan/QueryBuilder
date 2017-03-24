<?php
/**
 * Created by Arsen Ter.Oganisyan
 */

namespace Builder\Bundle\Ffc\QueryBuilder;

use Builder\Bundle\Ffc\Exception\TransferQueryBuilderException;
use Builder\Bundle\Ffc\QueryBuilder\Condition\Condition;

/**
 * Default query builder
 */
class DefaultBuilder implements BuilderInterface
{

    /**
     * Composite class name
     *
     * @var string
     */
    private $compositeClassName;

    /**
     * Item class name
     *
     * @var string
     */
    private $itemClassName;

    /**
     * Compiled queries
     *
     * @var array
     */
    private $compiledQueries = array();

    /**
     * Temporary repository
     *
     * @var array
     */
    private $tmp;

    /**
     * Query fields
     *
     * @var array
     */
    protected $fields = array(
        'srcAccount',
        'dstAccount',
        'transferType',
        'extTransferId',
        'id',
        'createTs',
        'approveTs',
        'extTransferId'
    );

    /**
     * Fields alias
     *
     * @var string
     */
    protected $fieldAlias = 't';

    /**
     * Constructor
     *
     * @param string $compositeClassName composite class name
     * @param string $itemClassName item class name
     */
    public function __construct($compositeClassName, $itemClassName)
    {
        $this->compositeClassName = $compositeClassName;
        $this->itemClassName      = $itemClassName;
    }

    /**
     * {@inheritdoc}
     */
    public function getDql(array $query)
    {
        if (!isset($this->compiledQueries[md5(serialize($query))])) {
            $this->process($query);
        }
        return $this->compiledQueries[md5(serialize($query))]['dql'];
    }

    /**
     * {@inheritdoc}
     */
    public function getBinds(array $query)
    {
        if (!isset($this->compiledQueries[md5(serialize($query))])) {
            $this->process($query);
        }
        return $this->compiledQueries[md5(serialize($query))]['binds'];
    }

    /**
     * Processes query
     *
     * @param array $query query
     */
    protected function process(array $query)
    {
        if (!isset($query['conditions']) or !is_array($query['conditions'])) {
            throw new TransferQueryBuilderException('Query conditions part not isset');
        }

        $queryId = md5(serialize($query));

        $obj = $this->getQueryObj($query['conditions'], $queryId);
        $this->compiledQueries[$queryId]['dql'] = $obj->getConditionString();
        $this->tmp = array();
    }

    /**
     * Parses conditions
     *
     * @param array $conditions
     * @param string $queryId query ID
     *
     * @return mixed
     *
     * @throws \Bundle\Bundle\Ffc\Exception\TransferQueryBuilderException
     */
    protected function getQueryObj(array $conditions, $queryId)
    {
        if (isset($conditions['subList']) and is_array($conditions['subList'])) {
            if (!in_array($conditions['operation'], array('or', 'and'))) {
                throw new TransferQueryBuilderException('Query operation is not defined');
            }

            $class = $this->compositeClassName;
            $cl    = new $class($conditions['operation']);

            foreach ($conditions['subList'] as $condition) {
                $cl->add($this->getQueryObj($condition, $queryId));
            }

            return $cl;
        } elseif (isset($conditions['condition']) and is_array($conditions['condition'])) {
            if (!isset($conditions['condition']['field'])) {
                throw new TransferQueryBuilderException('Condition field is not set');
            }

            if (!isset($conditions['condition']['values']) or !is_array($conditions['condition']['values'])) {
                throw new TransferQueryBuilderException('Condition values are not set');
            }

            if (!isset($conditions['condition']['type'])) {
                throw new TransferQueryBuilderException('Condition operation type is not set');
            }

            $prepStatementConditions = array();
            foreach ($conditions['condition']['values'] as $value) {
                if (!isset($this->tmp[$conditions['condition']['field']]['counter'])) {
                    $this->tmp[$conditions['condition']['field']]['counter'] = 0;
                }
                $this->tmp[$conditions['condition']['field']]['counter']++;
                $counter = $this->tmp[$conditions['condition']['field']]['counter'];
                $prepStatementConditions[] = $conditions['condition']['field'].$counter;

                $this->compiledQueries[$queryId]['binds'][Condition::cleanPlaceHolder($conditions['condition']['field'].$counter)] = $value;
            }

            $class = $this->itemClassName;
            $cl    = new $class(
                $conditions['condition']['field'],
                $prepStatementConditions,
                $conditions['condition']['type'],
                $this->fieldAlias
            );
            return $cl;
        }

        throw new TransferQueryBuilderException('Wrong query structure');
    }
}
