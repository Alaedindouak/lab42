<?php

namespace Alaedin\Lab42\Tests;

use Alaedin\Lab42\Container;
use Alaedin\Lab42\Tests\Fixtures\Configuration;
use Alaedin\Lab42\Tests\Fixtures\Database;
use Alaedin\Lab42\Tests\Fixtures\Router;
use Alaedin\Lab42\Tests\Fixtures\RouterInterface;

use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{

    public function test()
    {
        $container = new Container();

        $container->addService(RouterInterface::class, Router::class);

        $container->getDefinition(Configuration::class)->setShared(false);


        $this->assertInstanceOf(Database::class, $container->get(Database::class));
        $this->assertInstanceOf(Router::class, $container->get(Router::class));


        $db1 = $container->get(Database::class);
        $db2 = $container->get(Database::class);

        $this->assertEquals(spl_object_id($db1), spl_object_id($db2));

        $this->assertInstanceOf(Router::class, $container->get(RouterInterface::class));


        $config1 = $container->get(Configuration::class);
        $config2 = $container->get(Configuration::class);

        $this->assertNotEquals(spl_object_id($config1), spl_object_id($config2));
    }
}

