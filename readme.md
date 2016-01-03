# Mako Schema Builder

This is a simple database schema builder package for Mako Framework >=4.5.

This package runs on top of [Phinx](https://phinx.org/)

## Install

Use composer to install. Simply add package to your project.

```php
composer require softr/mako-schema-builder:*
```

So now you can update your project with a single command.

```php
composer update
```


### Register Service

After installing you'll have to register the package in your ``app/config/application.php`` file.

```
'packages' =>
[
    ...
    'web' =>
    [
        ...
        // Register the package for web app
        'softr\MakoSchemaBuilder\SchemaPackage',
    ],
    'cli' =>
    [
        ...
        // Register the package for command line app
        'softr\MakoSchemaBuilder\SchemaPackage',
    ]
],
```

## Creating Tables

To create a new database table, use the ``create`` method. The create method accepts two arguments. The first is the name of the table, while the second is a Closure which receives a object used to define the new table:

```php
$this->schema->create('users', function($table)
{
    $table
        ->addColumn('username', 'string', ['limit' => 20])
        ->addColumn('password', 'string', ['limit' => 40])
        ->addColumn('password_salt', 'string', ['limit' => 40])
        ->addColumn('email', 'string', ['limit' => 100])
        ->addIndex(['username', 'email'], ['unique' => true]);
});
```

## Modifying Tables

To modify an existing table, use the ``table`` method. The table method accepts two arguments. The first is the name of the table, while the second is a Closure which receives a object used to define the table wich will be modified:

```php
$this->schema->table('users', function($table)
{
    $table->addColumn('username', 'string', ['limit' => 20])
          ->changeColumn('email', 'string', ['limit' => 255]);
});
```

## Connection

You can also specify an alternative connection. Just use the ``connection`` method specifing the connection name.

```php
$this->schema->connection('foo')->create('table', function($table)
{
    ....
});
```

## Renaming / Dropping Tables

To rename an existing database table, use the ``rename`` method:

```php
$this->schema->rename('from', 'to');
```

To drop an existing table, you may use the drop or dropIfExists methods:

```php
$this->schema->drop('users');

$this->schema->dropIfExists('users');
```

## Checking For Table / Column Existence

You may easily check for the existence of a table or column using the ``hasTable``, ``hasColumn`` and ``hasColumns`` methods:

```php
if($this->schema->hasTable('users'))
{
    // Do something
}

if($this->schema->hasColumn('users', 'email'))
{
    // Do something
}

if($this->schema->hasColumns('users', ['name', 'email']))
{
    // Only returns true if all fields exists on table
}
```

## Working With Columns

#### Get a column list

To retrieve all table columns, simply call the ``getTableColumns`` method. This method will return an array of column names.

```php
$columns = $this->schema->getTableColumns('users');
```

#### Renaming a Column

To rename a column access an instance of the Table object then call the ``renameColumn`` method.

```php
$table->renameColumn('bio', 'biography');
```

#### Adding a Column After Another Column

When adding a column you can dictate its position using the after option.

```php
$table->addColumn('city', 'string', ['after' => 'email']);
```

#### Dropping a Column

To drop a column, use the ``removeColumn`` method.

```php
$table->removeColumn('short_name');
```

#### Specifying a Column Limit

You can limit the maximum length of a column by using the limit option.

```php
$table->addColumn('short_name', 'string', ['limit' => 30]);
```

#### Changing Column Attributes

To change column type or options on an existing column, use the ``changeColumn`` method.

```php
$table->changeColumn('email', 'string', ['limit' => 255]);
```

## Working with Indexes

To add an index to a table you can simply call the addIndex() method on the table object.

```php
$table->addIndex(['email']);
```

We can pass an additional parameter to the addIndex() method to specify a unique index.

```php
$table->addIndex(['email'], ['unique' => true]);
```

Removing indexes is as easy as calling the removeIndex() method. You must call this method for each index.

```php
$table->removeIndex(['email']);
```

## Working With Foreign Keys

Phinx has support for creating foreign key constraints on your database tables. Let’s add a foreign key to an example table:

```php
$this->schema->create('tags', function($table)
{
    $table->addColumn('tag_name', 'string')
});

$this->schema->create('tag_relationships', function($table)
{
    $table->addColumn('tag_id', 'integer')
          ->addForeignKey('tag_id', 'tags', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION']);
});
```

It is also possible to pass ``addForeignKey`` an array of columns. This allows us to establish a foreign key relationship to a table which uses a combined key.

```php
$this->schema->table('follower_events', function($table)
{
    $table->addColumn('user_id', 'integer')
          ->addColumn('follower_id', 'integer')
          ->addColumn('event_id', 'integer')
          ->addForeignKey(['user_id', 'follower_id'], 'followers', ['user_id', 'follower_id'], ['delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION']);
});
```

We can also easily check if a foreign key exists:

```php
$this->schema->table('tag_relationships', function($table)
{
    if($table->hasForeignKey('tag_id'))
    {
        // do something
    }
});
```

Finally to delete a foreign key use the ``dropForeignKey`` method.

```php
$this->schema->table('tag_relationships', function($table)
{
    $table->dropForeignKey('tag_id');
});
```

## Valid Column Types

For a full accepted column types please check the oficial documentation avaliable [on this link](http://docs.phinx.org/en/latest/migrations.html#valid-column-types)

## Limitations

This package was tested only with ``MySQL`` databases. Please feel free to contribute to this project.