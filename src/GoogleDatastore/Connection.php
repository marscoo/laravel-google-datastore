<?php

namespace GoogleDatastore;

use GDS\Store as Store;

class Connection extends \Illuminate\Database\Connection
{

    /**
     * @var type
     */
    protected $googleClient;

    /**
     * @var type
     */
    protected $googleGateway;

    /**
     * Create a new database connection instance.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {

        //Set the config
        $this->config = $config;

        // Create the connection
        $this->connection = $this->createConnection();

        //Use default grammar and post processor.
        $this->useDefaultQueryGrammar();
        $this->useDefaultPostProcessor();
    }

    /**
     * Get the PDO driver name.
     *
     * @return string
     */
    public function getDriverName()
    {
        return 'googledatastore';
    }

    /**
     * @param type $table
     *
     * @return Query\Builder
     */
    public function table($table)
    {
        return $this->query()->from($table);
    }

    /**
     * Get a new query builder instance.
     *
     * @return GoogleDatastore\Query\Builder
     */
    public function query()
    {
        return new \GoogleDatastore\Query\Builder(
            $this, $this->getQueryGrammar(), $this->getPostProcessor()
        );
    }

    /**
     * @param type $kind
     *
     * @return type
     */
    public function kind($kind)
    {
        return $this->table($kind);
    }

    /**
     * Run a select query on Datastore
     * 
     * @param string $query
     * @param array $bindings
     * 
     */
    public function select($query, $bindings = [], $from = null)
    {
        return $this->run($query, $bindings, function ($me, $query, $bindings) use ($from) {

                if ($me->pretending()) {
                    return [];
                }

                //Create the GS Store.
                $store = new Store($from, $this->googleGateway);

                //Run the query
                $runQuery = $store->fetchAll($query);

                return $runQuery;
            });
    }

    /**
     * Get the default query grammar instance.
     *
     * @return \Illuminate\Database\Query\Grammars\Grammar
     */
    protected function getDefaultQueryGrammar()
    {
        return new \GoogleDatastore\Query\Grammar();
    }

    /**
     * Get the default post processor instance.
     *
     * @return Query\Processor
     */
    protected function getDefaultPostProcessor()
    {
        return new \GoogleDatastore\Query\Processor();
    }

    /**
     * Get the gateway used for connecting to Google.
     *
     * @return \GDS\Gateway\GoogleAPIClient
     */
    public function getGoogleGateway()
    {
        return $this->googleGateway;
    }

    /**
     * Create a new datastore connection.
     * 
     * @return \GDS\Gateway\GoogleAPIClient
     */
    protected function createConnection()
    {

        // We'll need a Google_Client, use our convenience method
        $this->googleClient = \GDS\Gateway\GoogleAPIClient::createGoogleClient($this->config['appname'], $this->config['service_email'], base_path() . '/resources/assets/' . $this->config['key_file']);

        //THE GATEWAY TO USE
        $this->googleGateway = new \GDS\Gateway\GoogleAPIClient($this->googleClient, $this->config['project_id']);

        return $this->googleClient;
    }

    /**
     * Dynamically pass methods to the connection.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this, $method], $parameters);
    }
}
