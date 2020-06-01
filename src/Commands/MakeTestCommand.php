<?php

namespace Aphpi\Template\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeTestCommand extends GeneratorCommand
{
    protected $name = 'make:test';
    protected $description = 'Creates a new Test';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $this->qualifyClass(trim($input->getArgument('name')));
        $return = parent::execute($input, $output);

        $output->writeln('<info>Test ' . $name . ' successfully created</info>');

        // option --test

        return $return;
    }

    protected function rootNamespace() : string
    {
        if (isset($this->rootNamespace)) {
            return $this->rootNamespace;
        }

        $composer = json_decode(file_get_contents('composer.json'), true);

        return $this->rootNamespace = array_keys($composer['autoload-dev']['psr-4'])[0];
    }

    protected function getStub() : string
    {
        return 'src/stubs/test.stub';
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

        return 'tests/' . str_replace('\\', '/', $name).'.php';
    }
}