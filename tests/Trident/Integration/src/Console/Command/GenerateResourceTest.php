<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\WorkflowRestfulCrud;
use j0hnys\Trident\Builders\Resource;
use j0hnys\Trident\Console\Commands\GenerateResource;

class GenerateResourceTest extends TestCase
{
    private $workflow_restful_crud;
    private $td_entity_name;
    private $resources;

    public function setUp(): void
    {
        parent::setUp();

        $this->td_entity_name = 'DemoProcess';

        $install = new Install($this->storage_disk);
        $install->run();

        //for workflow restful crud
        $this->storage_disk->makeDirectory($this->base_path.'/app/Models/.');

        $stub = $this->storage_disk->readFile($this->base_path.'/../Stubs/App/Models/DemoProcess.stub');
        $this->storage_disk->writeFile($this->base_path.'/app/Models/DemoProcess.php', $stub);
        exec('composer dump-autoload');

        $this->workflow_restful_crud = new WorkflowRestfulCrud($this->storage_disk, $this->storage_trident);
        $mock_command = $this->createMock(\Illuminate\Console\Command::class);

        $schema = [
            'functionality_schema_path' => '',
            'validation_schema_path' => '',
            'strict_type_schema_path' => '',
            'resource_schema_path' => '',
        ];
        $this->workflow_restful_crud->generate($this->td_entity_name, $schema, $mock_command);

        //policy function
        $this->resources = new Resource($this->storage_disk);

        //command behavioural test
        $this->mock_resources = $this->createMock(Resource::class);
        $this->mock_command_resources = $this->getMockBuilder(GenerateResource::class)
            ->setConstructorArgs([$this->mock_resources])
            ->setMethods(['argument','option','info'])
            ->getMock();
    }


    public function testHandle()
    {
        $entity_name = '';
        $function_name = '';
        $is_collection = '';
        $domain = '';
        $schema_path = '';
        $force = false;

        $this->mock_command_resources->expects($this->at(0))
            ->method('argument')
            ->willReturn($entity_name);

        $this->mock_command_resources->expects($this->at(1))
            ->method('argument')
            ->willReturn($function_name);

        $this->mock_command_resources->expects($this->at(2))
            ->method('option')
            ->willReturn($is_collection);

        $this->mock_command_resources->expects($this->at(3))
            ->method('option')
            ->willReturn($domain);

        $this->mock_command_resources->expects($this->at(4))
            ->method('option')
            ->willReturn($schema_path);

        $this->mock_command_resources->expects($this->at(5))
            ->method('option')
            ->willReturn($force);

        $this->mock_command_resources->expects($this->at(0))
            ->method('info')
            ->willReturn(null);

        $this->mock_command_resources->handle();

        //assert
        $this->assertTrue(true);
    }


    public function testGenerate()
    {
        $is_collection = 'Resource';
        $domain = 'Workflows';
        $schema_path = $this->base_path.'/../Stubs/_Solution/Schemas/DemoProcess/Resource/Response.json';
        $force = false;
        
        $this->resources->generate($this->td_entity_name, $is_collection, $domain, $schema_path, $force);

        $this->assertTrue(true);
    }

}
