<?php

namespace App\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MovieTest extends WebTestCase
{
    public function testItShowsTheMovieList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/movies');

        $this->assertResponseIsSuccessful();

        // Verify the number of displayed movies
        $expectedMoviesCount = 3;
        $movieNodes = $client->getCrawler()->filter('ul.movies li');

        $this->assertCount($expectedMoviesCount, $movieNodes);

        $firstMovieHref = $movieNodes->eq(0)->filter('a')->attr('href');
        $this->assertStringStartsWith('/movies/', $firstMovieHref);
    }

    public function testItShowsAMovieDetails(): void
    {
        $client = static::createClient();
        $client->request('GET', '/movies/1');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('div.details', 'The Matrix');
        $this->assertSelectorTextContains('div.details', 'A computer hacker learns from mysterious rebels');
        $this->assertSelectorTextContains('div.details', 'Genre : Action');
    }

    public function testItThrowsANotFoundExceptionWhenMovieDoesNotExist(): void
    {
        $client = static::createClient();
        $client->request('GET', '/movies/666');

        $this->assertResponseStatusCodeSame(404);
    }
}
