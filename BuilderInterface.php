<?php
/**
 * Created by Arsen Ter.Oganisyan
 */

namespace Builder\Bundle\Ffc\QueryBuilder;

/**
 * Builder interface
 */
interface BuilderInterface
{
    /**
     * Gets dql by query
     *
     * @param array $query query
     *
     * @return string
     */
    public function getDql(array $query);

    /**
     * Gets binds by query
     *
     * @param array $query query
     *
     * @return array
     */
    public function getBinds(array $query);
}
