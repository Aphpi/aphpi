<?php

namespace Aphpi\Template\Commands\Generate;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OpenApiCommand extends Command
{
    const FUNCTION_NAMES = [
        'delete' => 'destroy',
        'patch' => 'update',
        'post' => 'store',
        'put' => 'update',
    ];

    const VARIABLE_TYPES = [
        'integer' => 'int',
        'string' => 'string',
    ];

    protected $name = 'generate:openapi';
    protected $description = 'Generates Endpoints from OpenAPI JSON';

    protected function configure()
    {
        $this->setName($this->name)
            ->setDescription($this->description)
            ->addArgument('filename', null, InputArgument::REQUIRED)
            ->addOption('test', null, InputOption::VALUE_OPTIONAL, '', false);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->input = $input;

        $this->makeEndpointCommand = $this->getApplication()->find('make:endpoint');
        $this->makeTestCommand = $this->getApplication()->find('make:test');

        $filename = trim($input->getArgument('filename'));

        $openapi = json_decode(file_get_contents($filename), true);

        $rootEndpoints = $this->rootEndpoints($openapi['paths']);

        $endpoint_namespaces = [];
        foreach ($rootEndpoints as $key => $endpoint) {
            $endpoint_namespaces[] = $this->createEndpoint($endpoint['uri'], $endpoint['path'], $endpoint['endpoints']);
        }

        $this->replaceApi($openapi['host'], $endpoint_namespaces);
        $this->replaceClient($openapi['basePath']);

        return 0;
    }

    protected function rootEndpoints(array $paths) : array
    {
        $rootEndpoints = [];
        foreach ($paths as $uri => $path) {
            $loc = &$rootEndpoints;
            $name = substr(preg_replace('/\/\{\w+\}/', '', $uri), 1);
            $parts = explode('/', $name);
            $parts_count = count($parts);
            $max_key = ($parts_count - 1);
            foreach ($parts as $key => $part) {
                if (! array_key_exists($part, $loc)) {
                    $loc[$part] = [
                        'endpoints' => [],
                        'path' => [],
                        'uri' => '',
                    ];
                }

                if ($key == $max_key) {
                    $loc = &$loc[$part];
                }
                else {
                    $loc = &$loc[$part]['endpoints'];
                }
            }

            $loc['uri'] = $uri;
            $loc['path'] = $path;
        }

        ksort($rootEndpoints);

        return $rootEndpoints;
    }

    protected function replaceApi(string $host, array $endpoints)
    {
        $path = 'src/Api.php';

        sort($endpoints);

        $endpoints_string = [];
        foreach ($endpoints as $key => $namespace) {
            $endpoints_string[] = '$this->' . str_replace('\\', '', $namespace) . ' = new Endpoints\\' . $namespace . '($client);';
        }

        $content = file_get_contents($path);
        file_put_contents($path, str_replace(['httpbin.org', '$this->example = new Example($client);'], [$host, implode("\n", $endpoints_string)], $content));
    }

    protected function replaceClient(string $base_path)
    {
        if (! $base_path) {
            return;
        }

        $path = 'src/Client.php';

        $functions = [];
        $functions['base_path'] = "protected function pathPrefix() : string { return '" . $base_path . "'; }";
        $content = file_get_contents($path);
        file_put_contents($path, str_replace('//', implode("\n", $functions), $content));
    }

    protected function createEndpoint(string $uri, array $path, array $endpoints) : string
    {
        $name = substr(preg_replace('/\/\{\w+\}/', '', $uri), 1);
        $namespace = implode('\\', array_map('ucfirst', explode('/', $name)));

        $parameters = (array_key_exists('parameters', $path) ? $path['parameters'] : []);
        unset($path['parameters']);

        $functions = [];
        $tests = [];
        foreach ($path as $verb => $request) {
            $functions[$verb] = $this->buildFunction($uri, $verb, $parameters);
            $tests[$verb] = $this->buildTest($namespace, $uri, $verb, $parameters);
        }

        $endpoint_namespaces = [];
        foreach ($endpoints as $key => $endpoint) {
            $endpoint_namespaces[$key] = $this->createEndpoint($endpoint['uri'], $endpoint['path'], $endpoint['endpoints']);
            break;
        }

        $this->makeEndpointCommand->run(new ArrayInput([
            'name' => $namespace,
            '--content' => $functions,
            '--endpoints' => $endpoint_namespaces,
        ]), $this->output);

        $this->makeTestCommand->run(new ArrayInput([
            'name' => 'Endpoints\\' . $namespace . 'Test',
            '--content' => $tests,
        ]), $this->output);


        return $namespace;
    }

    protected function buildFunction(string $uri, string $verb, array $parameters) : string
    {
        if ($verb == 'get') {
            $name = (substr($uri, -1) == '}' ? 'show' : 'index');
        }
        else {
            $name = self::FUNCTION_NAMES[$verb];
        }

        $function = 'public function ' . $name . ' (';
        $parameters_string = [];
        $parameters_string2 = [];
        foreach ($parameters as $key => $parameter) {
            $parameters_string[] = '?' . self::VARIABLE_TYPES[$parameter['type']] . ' $' . $parameter['name'] . ' = null';
            if ($parameter['in'] == 'path') {
                continue;
            }
            $parameters_string2[] = "'" . $parameter['name'] . "' => $" . $parameter['name'];
        }

        $urlWithVars = preg_replace('/\{(\w+)\}/', '" . ($${1} ?? $this->$${1}) . "', $uri);
        $function .= implode(', ', $parameters_string) . ') { return $this->client->' . $verb . '("' . $urlWithVars . '", [' . implode(', ', $parameters_string2) . ']); }';

        return $function;
    }

    /** * @test */
    public function it_works()
    {
        $data = $this->api->example->post();
        $this->assertEquals('https://httpbin.org/post', $data['url']);
    }

    protected function buildTest(string $name, string $uri, string $verb, array $parameters) : string
    {
        if ($verb == 'get') {
            $function_name = (substr($uri, -1) == '}' ? 'show' : 'index');
        }
        else {
            $function_name = self::FUNCTION_NAMES[$verb];
        }

        $function = '';
        $parameters_string = [];
        $parameters_string2 = [];
        foreach ($parameters as $key => $parameter) {
            $parameters_string[] = '$' . $parameter['name'];
        }

        $function .=  '/** * @test */ public function ' . $function_name . '_test () { $this->markTestIncomplete(); $response = $this->api->' . str_replace('\\', '', $name) . '->' . $function_name. '(' . implode(', ', $parameters_string) . '); }';

        return $function;
    }

}