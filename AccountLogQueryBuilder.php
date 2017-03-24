<?php
/**
 * Created by Arsen Ter.Oganisyan
 */

namespace Builder\Bundle\Ffc\QueryBuilder;

/**
 * Class AccountLogQueryBuilder
 * @package Builder\Bundle\Ffc\QueryBuilder
 */
class AccountLogQueryBuilder extends DefaultBuilder implements BuilderInterface
{
    /**
     * Query fields
     *
     * @var array
     */
    protected $fields = array(
        'a.id',
        'a.balance',
        'a.credit',
        'a.number',
        'a.code',
        'a.statusId',
        'a.franchiseeClientId',
        'a.franchiseeId',
        'l.id',
        'l.transfer',
        'l.amount',
        'l.ts',
        'l.comment'
    );

    /**
     * Fields alias
     *
     * @var string
     */
    protected $fieldAlias = '';

}
