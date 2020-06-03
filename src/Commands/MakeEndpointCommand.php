<?php

namespace Aphpi\Template\Commands;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeEndpointCommand extends GeneratorCommand
{
    protected $name = 'make:endpoint';
    protected $description = 'Creates a new Endpoint';

    protected function configure()
    {
        parent::configure();

        $this->addOption('test', null, InputOption::VALUE_OPTIONAL, '', false)
            ->addOption('endpoints', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY);;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $return = parent::execute($input, $output);

        $name = $this->qualifyClass(trim($input->getArgument('name')));

        $output->writeln('<info>Endpoint ' . $name . ' successfully created</info>');

        if ($input->getOption('test') !== false) {
            $command = $this->getApplication()->find('make:test');
            $command->run(new ArrayInput([
                'name' => 'Endpoints\\' . trim($input->getArgument('name')) . 'Test',
            ]), $output);
        }

        return $return;
    }

    protected function buildClass($name)
    {
        $stub = file_get_contents($this->getStub());

        return $this->replaceNamespace($stub, $name)
            ->addContent($stub)
            ->addEndpoints($stub)
            ->replaceClass($stub, $name);
    }

    protected function addEndpoints(&$stub)
    {
        $endpoints = $this->input->getOption('endpoints');
        if (! $endpoints) {
            $endpoints = [];
        }

        $properties = [];
        $setEndpoints = [];

        foreach ($endpoints as $name => $namespace) {
            $properties[] = 'public $' . $name . ';';
            $setEndpoints[] = '$this->' . $name . ' = new ' . $namespace . '($this->client, $this->attributes);';
        }

        if (empty($setEndpoints)) {
            $setEndpoints[] = '//';
        }

        $stub = str_replace(
            ['DummyProperties', 'DummyEndpoints'],
            [implode("\n", $properties), implode("\n", $setEndpoints)],
            $stub
        );

        return $this;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Endpoints';
    }

    protected function rootNamespace() : string
    {
        if (isset($this->rootNamespace)) {
            return $this->rootNamespace;
        }

        $composer = json_decode(file_get_contents('composer.json'), true);

        return $this->rootNamespace = array_keys($composer['autoload']['psr-4'])[0];
    }

    protected function getStub() : string
    {
        return 'src/stubs/endpoint.stub';
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace($this->rootNamespace(), '', $name);

        return 'src/' . str_replace('\\', '/', $name).'.php';
    }
}