<?php

/**
 * @copyright  Agência Softr <agencia.softr@gmail.com>
 * @license    http://www.makoframework.com/license
 */
namespace softr\MakoSchemaBuilder;


use \Closure;

// Mako
use mako\config\Config;
use mako\database\ConnectionManager;


// Mako Schema Builder
use softr\MakoSchemaBuilder\Resolver\Connector;
use softr\MakoSchemaBuilder\Resolver\Builder;


/**
 * Schema Builder
 *
 * @author     Aldo Anizio Lugão Camacho
 * @copyright  (c) 2016
 */
class Schema
{
    /**
     * Phinx Adapter Connector instance.
     *
     * @var \softr\MakoSchemaBuilder\Resolver\Connector
     */
    protected $connector;

    /**
     * Constructor.
     *
     * @access  public
     * @param   \mako\database\ConnectionManager  $connectionManager  Connection manager instance
     * @param   \mako\config\Config               $config             Config instance
     */
    public function __construct(ConnectionManager $connectionManager, Config $config)
    {
        $this->connector = new Connector($connectionManager);

        $config = $config->get('mako-schema-builder::config');

        $this->connector->setOptions($config);
    }

    //---------------------------------------------
    // Class methods
    //---------------------------------------------

    /**
     * Determine if the given table exists.
     *
     * @param   string  $table  Table name
     * @return  bool
     */
    public function hasTable($table)
    {
        if($this->connector->getAdapter()->hasTable($table))
        {
            return true;
        }

        return false;
    }

    /**
     * Determine if the given table has a given column.
     *
     * @param   string  $table   Table name
     * @param   string  $column  Column name
     * @return  bool
     */
    public function hasColumn($table, $column)
    {
        return $this->connector->getAdapter()->hasColumn($table, $column);
    }

    /**
     * Determine if the given table has given columns.
     *
     * @param   string  $table    Table name
     * @param   array   $columns  Array of columns
     * @return  bool
     */
    public function hasColumns($table, array $columns)
    {
        $tableColumns = array_map('strtolower', $this->getTableColumns($table));

        foreach($columns as $column)
        {
            if(!in_array(strtolower($column), $tableColumns))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the column listing for a given table.
     *
     * @param   string  $table  Table name
     * @param   string  $raw    (optional) Returns each item as a \Phinx\Db\Table\Column Object instead column name
     * @return  array
     */
    public function getTableColumns($table, $raw = false)
    {
        if($raw === true)
        {
            return $this->connector->getAdapter()->getColumns($table);
        }

        return array_map(function($column)
        {
            return $column->getName();

        }, $this->connector->getAdapter()->getColumns($table));
    }

    /**
     * Drop a table from the schema.
     *
     * @param  string  $table  Table name
     */
    public function dropTable($table)
    {
        $this->connector->getAdapter()->dropTable($tableName);
    }

    /**
     * Drop a table from the schema if it exists.
     *
     * @param  string  $table  Table name
     */
    public function dropIfExists($table)
    {
        if($this->connector->getAdapter()->hasTable($table))
        {
            $this->connector->getAdapter()->dropTable($tableName);
        }
    }

    /**
     * Rename a table on the schema.
     *
     * @param  string  $from
     * @param  string  $to
     */
    public function renameTable($from, $to)
    {
        $this->connector->getAdapter()->renameTable($from, $to);
    }

    /**
     * Modify a table on the schema.
     *
     * @param  string    $table     Table name
     * @param  \Closure  $callback  Callback functions
     */
    public function table($table, Closure $callback)
    {
        $this->builder($table, $callback)->save();
    }

    /**
     * Create a new table on the schema.
     *
     * @param  string    $table     Table name
     * @param  \Closure  $callback  Callback functions
     */
    public function create($table, Closure $callback)
    {
        $this->builder($table, $callback)->create();
    }

    /**
     * Returns a table instance.
     *
     * @access  protected
     * @param   \Phinx\Db\Table  $table     Table Instance
     * @param   mixed            $callback  Callback functions
     */
    public function builder($table, Closure $callback)
    {
        $builder = new Builder($table, $this->connector->getAdapter(), $callback);

        return $builder->getTable();
    }

    /**
     * Set wich connection would be used.
     *
     * @access  public
     * @param   string   $connectionName  Connection name
     * @return  \Schema
     */
    public function connection($connectionName)
    {
        $this->connector->setConnectionName($connectionName);

        return $this;
    }
}