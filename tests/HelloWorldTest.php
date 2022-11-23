<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HelloWorldTest extends WebTestCase
{
    public function provideHelloCases(): array
    {
        return [
            ['/ジョン・ドウ', 'ジョン・ドウ'],
            ['/Adrien', 'Adrien'],
            ['', 'World']
        ];
    }


    /**
     * @dataProvider provideHelloCases
     */
    public function testItSaysHello(string $urlSuffix, string $expectedName): void
    {
        $client = static::createClient();
        $client->request('GET', '/hello'.$urlSuffix);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Hello '.$expectedName);
    }
}
