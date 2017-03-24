<?php
/**
 * Created by Arsen Ter.Oganisyan
 */

namespace Builder\Bundle\Ffc\QueryBuilder\Condition;

use Builder\Bundle\Ffc\Exception\TransferQueryBuilderException;

/**
 * Condition class
 */
class Condition implements ConditionListInterface
{

    /**
     * Collection of sub collection
     *
     * @var array
     */
    private $list = array();

    /**
     * Field of condition
     *
     * @var string
     */
    private $field;

    /**
     * Opeation type
     *
     * @var string
     */
    private $type;

    /**
     * Fields prefix
     *
     * @var string
     */
    private $fieldPrefix;

    /**
     * Cleans placeholder
     *
     * @param string $placeHolder placeholder
     *
     * @return string
     */
    public static function cleanPlaceHolder($placeHolder)
    {
        return str_replace('.', '', $placeHolder);
    }

    /**
     * Constructor
     *
     * @param string $field      field for condition
     *
     * @param array  $list       list
     *
     * @param string $type       operation type
     *
     * @param string $tableAlias table alias
     */
    public function __construct($field, array $list, $type, $tableAlias)
    {
        $this->type  = $type;
        $this->list  = $list;
        $this->field = $field;

        if ($tableAlias != '') {
            $this->fieldPrefix = $tableAlias . '.';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function add(ConditionListInterface $cl)
    {
        throw new TransferQueryBuilderException('Method add is not implemented for Condition class');
    }

    /**
     * {@inheritdoc}
     */
    public function getConditionString()
    {
        // @codingStandardsIgnoreStart
        switch ($this->type) {
            case 'IN':
                return $this->operationIn();
            case 'BETWEEN':
                return $this->operationBetween();
            case 'MORE':
                return $this->operationMore();
            case 'LESS':
                return $this->operationLess();
            case 'MORE_OR_EQUAL':
                return $this->operationMore(false);
            case 'LESS_OR_EQUAL':
                return $this->operationLess(false);
            case 'EQUAL':
                return $this->operationEqual();
        }
        throw new TransferQueryBuilderException('Unknown operation type: ' . $this->type);
        // @codingStandardsIgnoreEnd
    }

    /**
     * Operation IN
     *
     * @return string
     */
    private function operationIn()
    {
        return $this->fieldPrefix . "{$this->field} IN (:" . implode(
            ", :",
            $this->cleanPlaceHolders($this->list)
        ) . ")";
    }

    /**
     * Cleans placeholders of possible invalid chars
     *
     * @param array $placeHolders
     *
     * @return array
     */
    private function cleanPlaceHolders(array $placeHolders)
    {
        foreach ($placeHolders as &$placeHolder) {
            $placeHolder = self::cleanPlaceHolder($placeHolder);
        }

        return $placeHolders;
    }

    /**
     * Operation IN
     *
     * @return string
     */
    private function operationBetween()
    {
        $list = $this->cleanPlaceHolders(array_values($this->list));

        return $this->fieldPrefix . "{$this->field} BETWEEN :{$list[0]} and :{$list[1]}";
    }

    /**
     * Operation more
     *
     * @param bool $isStrict Strict greater or greater and equal
     *
     * @return string
     */
    private function operationMore($isStrict = true)
    {
        $list      = $this->cleanPlaceHolders(array_values($this->list));
        $condition = $isStrict ? '>' : '>=';

        return $this->fieldPrefix . "{$this->field} {$condition} :{$list[0]}";
    }

    /**
     * Operation less
     *
     * @param bool $isStrict Strict lesser or lesser and equal
     *
     * @return string
     */
    private function operationLess($isStrict = true)
    {
        $list      = $this->cleanPlaceHolders(array_values($this->list));
        $condition = $isStrict ? '<' : '<=';

        return $this->fieldPrefix . "{$this->field} {$condition} :{$list[0]}";
    }

    /**
     * Operation equal
     *
     * @return string
     */
    private function operationEqual()
    {
        $list = $this->cleanPlaceHolders(array_values($this->list));

        return $this->fieldPrefix . "{$this->field} = :{$list[0]}";
    }
}
