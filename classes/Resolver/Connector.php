<?php

/**
 * @copyright  Agência Softr <agencia.softr@gmail.com>
 * @license    http://www.makoframework.com/license
 */
namespace softr\MakoSchemaBuilder\Resolver;


use \PDO;

// Mako
use mako\database\ConnectionManager;


/**
 * Phinx Adapter Connector
 *
 * @author     Aldo Anizio Lugão Camacho
 * @copyright  (c) 2016
 */
class Connector
{
    /**
     * Class map of database adapters, indexed by PDO::ATTR_DRIVER_NAME.
     *
     * @var array
     */
    protected $phinxAdapters =
    [
        'mysql'  => 'Phinx\Db\Adapter\MysqlAdapter',
        'pgsql'  => 'Phinx\Db\Adapter\PostgresAdapter',
        'sqlite' => 'Phinx\Db\Adapter\SQLiteAdapter',
        'sqlsrv' => 'Phinx\Db\Adapter\SqlServerAdapter',
    ];

    /**
     * Connection name to use.
     *
     * @var string
     */
    protected $connectionName;

    /**
     * Connection manager instance.
     *
     * @var \mako\database\ConnectionManager
     */
    protected $connectionManager;

    /**
     * Adapter options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Constructor.
     *
     * @access  public
     * @param   \mako\database\ConnectionManager  $connectionManager  Connection manager instance
     * @param   array                             $options            (optional) Adapter options
     * @return  void
     */
    public function __construct(ConnectionManager $connectionManager, array $options = [])
    {
        $this->connectionManager = $connectionManager;

        if($options)
        {
            $this->setOptions($options);
        }
    }

    /**
     * Set wich connection would be used;
     *
     * @param   string  $options  Adapter options
     * @access  public
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Set wich connection would be used;
     *
     * @param   string  $connectionName  Connection name instance
     * @access  public
     */
    public function setConnectionName($connectionName)
    {
        $this->connectionName = $connectionName;
    }

    /**
     * Returns the connection.
     *
     * @access  protected
     * @return  \mako\database\Connection
     */
    protected function getConnection()
    {
        if(empty($this->connectionName))
        {
            return $this->connectionManager->connection();
        }

        return $this->connectionManager->connection($this->connectionName);
    }

    /**
     * Get current database name
     *
     * @return  string
     */
    private function getDatabaseName()
    {
        $query = $this->getConnection()->getPDO()->query('SELECT DATABASE()');

        $database = $query->fetch();

        return $database['DATABASE()'];
    }

    /**
     * Get table adapter instance
     *
     * @access  public
     * @return  \Phinx\Db\Adapter\AdapterInterface
     */
    public function getAdapter()
    {
        // PDO Instance

        $pdo = $this->getConnection()->getPDO();

        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $adapterOptions = $this->options +
        [
            'connection' => $pdo,
            'name'       => $this->getDatabaseName(),
        ];
        
        $adapterDrive = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

        $adapterClass = $this->getAdapterClass($adapterDrive);

        return new $adapterClass($adapterOptions);
    }

    /**
     * Get an adapter class by name.
     *
     * @access  public
     * @param   string  $name  Adapter name
     * @return  string
     */
    private function getAdapterClass($name)
    {
        if(!isset($this->phinxAdapters[$name]) || empty($this->phinxAdapters[$name]))
        {
            throw new \RuntimeException(sprintf('Adapter "%s" has not been registered', $name));
        }

        return $this->phinxAdapters[$name];
    }
}
