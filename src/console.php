<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Doctrine\DBAL\Schema\Table;

$console = new Application('CarService', '0.1.1');
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));
$console
    ->register('createdb')
    ->setDefinition(array(
        // new InputOption('some-option', null, InputOption::VALUE_NONE, 'Some help'),
    ))
    ->setDescription('Creates db and tables')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        $schema = $app['db']->getSchemaManager();

        if (!$schema->tablesExist('customers')) {
            $table = new Table('customers');
            $table->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
            $table->setPrimaryKey(array('id'));
            $table->addColumn('firstname', 'string', array('length' => 50));
            $table->addColumn('lastname', 'string', array('length' => 50));
            $table->addColumn('phonenumber', 'string', array('length' => 50));
            $table->addColumn('notes', 'text', array('length' => 255));
            $table->addColumn('created', 'datetime');
            $schema->createTable($table);
        }
        else {
            $output->writeln("Table customers already exists.");
        }

        if (!$schema->tablesExist('cars')) {
            $table = new Table('cars');
            $table->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
            $table->setPrimaryKey(array('id'));
            $table->addColumn('customer_id', 'integer', array('unsigned' => true));
            $table->addColumn('make', 'string', array('length' => 50));
            $table->addColumn('model', 'string', array('length' => 50));
            $table->addColumn('plate', 'string', array('length' => 10));
            $table->addColumn('notes', 'text', array('length' => 255));
            $table->addColumn('created', 'datetime');
            $schema->createTable($table);
        }
        else {
            $output->writeln("Table cars already exists.");
        }

        if (!$schema->tablesExist('workshop')) {
            $table = new Table('workshop');
            $table->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
            $table->setPrimaryKey(array('id'));
            $table->addColumn('car_id', 'integer', array('unsigned' => true));
            $table->addColumn('tasks', 'text');
            $table->addColumn('kms', 'string', array('length' => 12));
            $table->addColumn('startdate', 'datetime');
            $table->addColumn('enddate', 'datetime');
            $table->addColumn('done', 'integer', array('unsigned' => true));
            $table->addColumn('created', 'datetime');
            $schema->createTable($table);
        }
        else {
            $output->writeln("Table workshop already exists.");
        }
    })
;

return $console;