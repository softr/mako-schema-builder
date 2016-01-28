<?php

/**
 * @copyright  Agência Softr <agencia.softr@gmail.com>
 * @license    http://www.makoframework.com/license
 */
namespace softr\MakoSchemaBuilder;


// Schema package
use \softr\MakoSchemaBuilder\Schema;


/**
 * Schema package service.
 *
 * @author     Aldo Anizio Lugão Camacho
 * @copyright  (c) 2016
 */
class SchemaService extends \mako\application\services\Service
{
    /**
     * Register the service.
     *
     * @access  public
     */
    public function register()
    {
        $this->container->registerSingleton(['softr\MakoSchemaBuilder\Schema', 'schema'], function($container)
        {
            return new Schema($container->get('database'), $container->get('config'));
        });
    }
}