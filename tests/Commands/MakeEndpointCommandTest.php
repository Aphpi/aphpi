<?php

namespace Aphpi\Template\Tests\Endpoints;

use Aphpi\Template\Commands\MakeEndpointCommand;
use Aphpi\Template\Commands\MakeTestCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class MakeEndpointCommandTest extends TestCase
{
    public function test_it_can_make_an_endpoint()
    {
        $path = 'src/Endpoints/Foo';
        $filename = 'Bar.php';

        $app = new Application('aphpi cli');
        $app->add(new MakeEndpointCommand);

        $tester = new CommandTester($app->find('make:endpoint'));

        $statusCode = $tester->execute([
            'name' => 'Foo\\Bar',
        ]);

        $this->assertEquals($statusCode, 0);
        $this->assertDirectoryExists($path);
        $this->assertFileExists($path . '/' . $filename);

        unlink($path . '/' . $filename);
        rmdir($path);
    }

    /**
     * @test
     */
    public function it_can_make_an_endpoint_with_test()
    {
        $path = 'tests/Endpoints/Foo';
        $filename = 'BarTest.php';

        $app = new Application('aphpi cli');
        $app->add(new MakeEndpointCommand);
        $app->add(new MakeTestCommand);

        $tester = new CommandTester($app->find('make:endpoint'));

        $statusCode = $tester->execute([
            'name' => 'Foo\\Bar',
            '--test' => null,
        ]);

        $this->assertEquals($statusCode, 0);
        $this->assertDirectoryExists($path);
        $this->assertFileExists($path . '/' . $filename);

        unlink($path . '/' . $filename);
        rmdir($path);

        $path = 'src/Endpoints/Foo';
        $filename = 'Bar.php';

        unlink($path . '/' . $filename);
        rmdir($path);
    }

    /**
     * @test
     */
    public function it_can_make_an_endpoint_with_endpoints()
    {
        $path = 'src/Endpoints';
        $filename = 'Foo.php';

        $app = new Application('aphpi cli');
        $app->add(new MakeEndpointCommand);

        $tester = new CommandTester($app->find('make:endpoint'));

        $statusCode = $tester->execute([
            'name' => 'Foo',
            '--endpoints' => ['bar' => 'Aphpi\Template\Endpoints\Foo\Bar'],
        ]);

        $content = file_get_contents($path . '/' . $filename);

        $this->assertEquals($statusCode, 0);
        $this->assertDirectoryExists($path);
        $this->assertFileExists($path . '/' . $filename);
        $this->assertStringContainsString('public $bar;', $content);
        $this->assertStringContainsString('$this->bar = new Aphpi\Template\Endpoints\Foo\Bar($this->client, $this->attributes);', $content);

        unlink($path . '/' . $filename);
    }
}