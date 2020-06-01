<?php

namespace Aphpi\Template\Tests\Endpoints;

use Aphpi\Template\Commands\MakeEndpointCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class MakeEndpointCommandTest extends TestCase
{
    public function test_it_can_make_an_endpoint()
    {
        $path = 'src/Endpoints/Test';
        $filename = 'Endpoint.php';

        $app = new Application('aphpi cli');
        $app->add(new MakeEndpointCommand);

        $tester = new CommandTester($app->find('make:endpoint'));

        $statusCode = $tester->execute([
            'name' => 'Test\\Endpoint',
        ]);

        $this->assertEquals($statusCode, 0);
        $this->assertDirectoryExists($path);
        $this->assertFileExists($path . '/' . $filename);

        unlink($path . '/' . $filename);
        rmdir($path);
    }
}