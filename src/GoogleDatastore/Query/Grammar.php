<?php

namespace GoogleDatastore\Query;

use Illuminate\Database\Query\Grammars\Grammar as BaseGrammar;
use Illuminate\Database\Query\Builder;

class Grammar extends BaseGrammar
{

    /**
     * The components that make up a select clause.
     *
     * @var array
     */
    protected $selectComponents = [
        'columns',
        'from',
        'wheres',
        'orders',
        'limit',
    ];

    /**
     * Get the appropriate query parameter place-holder for a value.
     *
     * @param mixed $value
     *
     * @return string
     */
    public function parameter($value)
    {
        return "'" . $value . "'";
    }

    /**
     * Wrap a single string in keyword identifiers.
     *
     * @param string $value
     *
     * @return string
     */
    protected function wrapValue($value)
    {
        if ($value === '*') {
            return $value;
        }

        return str_replace('"', '""', $value);
    }

    /**
     * Compile the select
     * 
     * @param Builder $query
     */
    public function compileSelect(Builder $query)
    {
        return parent::compileSelect($query);
    }
}
