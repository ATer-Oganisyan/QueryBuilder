<?php
/**
 * Created by Arsen Ter.Oganisyan
 */

namespace Builder\Bundle\Ffc\QueryBuilder\Condition;

/**
 * Condition list interface
 */
interface ConditionListInterface
{
    /**
     * Adds child condition list
     *
     * @param ConditionListInterface $cl condition list
     *
     * @return void
     */
    public function add(ConditionListInterface $cl);

    /**
     * Gets condition string
     *
     * @return string
     */
    public function getConditionString();
}
