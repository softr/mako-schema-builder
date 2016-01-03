<?php

/**
 * @copyright  Agência Softr <agencia.softr@gmail.com>
 * @license    http://www.makoframework.com/license
 */
namespace softr\MakoSchemaBuilder;


// Schema package
use \softr\MakoSchemaBuilder\Schema;


/**
 * Schema package.
 *
 * @author     Aldo Anizio Lugão Camacho
 * @copyright  (c) 2016
 */
class SchemaPackage extends \mako\application\Package
{
    /**
     * Package name.
     *
     * @var string
     */
    protected $packageName = 'softr/mako-schema-builder';

    /**
     * Package namespace.
     *
     * @var string
     */
    protected $fileNamespace = 'mako-schema-builder';

    /**
     * Register the service.
     *
     * @access  protected
     */
    protected function bootstrap()
    {
        $this->container->registerSingleton(['softr\MakoSchemaBuilder\Schema', 'schema'], function($container)
        {
            return new Schema($container->get('database'), $container->get('config'));
        });
    }
}