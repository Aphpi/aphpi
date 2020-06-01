<?php

namespace Aphpi\Template\Tests\Endpoints;

use Aphpi\Template\Commands\MakeTestCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class MakeTestCommandTest extends TestCase
{
    public function test_it_can_make_an_endpoint()
    {
        $path = 'tests/Foo';
        $filename = 'BarTest.php';

        $app = new Application('aphpi cli');
        $app->add(new MakeTestCommand);

        $tester = new CommandTester($app->find('make:test'));

        $statusCode = $tester->execute([
            'name' => 'Foo\\BarTest',
        ]);

        $this->assertEquals($statusCode, 0);
        $this->assertDirectoryExists($path);
        $this->assertFileExists($path . '/' . $filename);

        unlink($path . '/' . $filename);
        rmdir($path);
    }
}