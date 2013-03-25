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
            $customers = new Table('customers');
            $customers->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
            $customers->setPrimaryKey(array('id'));
            $customers->addColumn('firstname', 'string', array('length' => 50));
            $customers->addColumn('lastname', 'string', array('length' => 50));
            $customers->addColumn('phonenumber', 'string', array('length' => 50));
            $customers->addColumn('notes', 'text', array('length' => 255));
            $customers->addColumn('created', 'datetime');
            $schema->createTable($customers);
        }
        else {
            $output->writeln("Table customers already exists.");
        }
    })
;

return $console;