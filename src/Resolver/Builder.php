<?php

/**
 * @copyright  Agência Softr <agencia.softr@gmail.com>
 * @license    http://www.makoframework.com/license
 */
namespace softr\MakoSchemaBuilder\Resolver;


use \Closure;
use \PDO;

// Phinx
use Phinx\Db\Table;
use Phinx\Db\Adapter\AdapterInterface;


/**
 * Table Builder
 *
 * @author     Aldo Anizio Lugão Camacho
 * @copyright  (c) 2016
 */
class Builder
{
    /**
     * Table instance.
     *
     * @var \Phinx\Db\Table
     */
    protected $table;

    /**
     * Constructor.
     *
     * @access  public
     * @param   string                              $table     Table name
     * @param   \Phinx\Db\Adapter\AdapterInterface  $adapter   Adapter instance
     * @param   \Closure                            $callback  Callback functions
     */
    public function __construct($table, AdapterInterface $adapter, Closure $callback)
    {
        $table = new Table($table, [], $adapter);

        $this->caller($table, $callback);

        $this->table = $table;
    }

    /**
     * Returns the table instance.
     *
     * @access  public
     * @return  \Phinx\Db\Table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Call the provided table builder.
     *
     * @access  protected
     * @param   \Phinx\Db\Table  $table     Table Instance
     * @param   mixed            $callback  Callback functions
     *
     * @throws  \InvalidArgumentException
     */
    protected function caller($table, $callback)
    {
        if($callback instanceof \Closure)
        {
            return call_user_func($callback, $table);
        }

        throw new \InvalidArgumentException('Callback is not valid.');
    }
}