<?php

namespace GoogleDatastore\Query;

use GoogleDatastore\Connection;
use Illuminate\Database\Query\Builder as BaseBuilder;

class Builder extends BaseBuilder
{
    /**
     * The current query value bindings.
     *
     * @var array
     */
    protected $bindings = [
        'select' => [],
        'where'  => [],
        'order'  => [],
    ];

    /**
     * A Builder object.
     *
     * @param Connection                                     $connection
     * @param \App\Providers\GoogleDatastore\Query\Grammar   $grammar
     * @param \App\Providers\GoogleDatastore\Query\Processor $processor
     */
    public function __construct(Connection $connection, Grammar $grammar, Processor $processor)
    {
        $this->grammar = $grammar;
        $this->connection = $connection;
        $this->processor = $processor;
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param array $columns
     *
     * @return array|static[]
     */
    public function get($columns = ['*'])
    {
        $original = $this->columns;

        if (is_null($original)) {
            $this->columns = $columns;
        }

        //Run the select query
        $results = $this->runSelect();

        $this->columns = $original;

        return $results;
    }

    /**
     * Run the query as a "select" statement against the connection.
     *
     * @return array
     */
    protected function runSelect()
    {
        return $this->connection->select($this->toGql(), $this->getBindings(), $this->from);
    }

    /**
     * Get the SQL representation of the query.
     *
     * @return string
     */
    public function toGql()
    {
        return $this->grammar->compileSelect($this);
    }
}
