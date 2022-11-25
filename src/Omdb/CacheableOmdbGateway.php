<?php
declare(strict_types=1);

namespace App\Omdb;

use App\Entity\Movie;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsDecorator(OmdbGateway::class, priority: 50)]
class CacheableOmdbGateway extends OmdbGateway
{
    const EXPIRES_AFTER = 100;

    public function __construct(
        private OmdbGateway $actualGateway,
        private CacheInterface $cache,
    )
    {
    }

    public function getMovieById(string $imdbId): array
    {
        $cacheKey = sprintf('imdbid_'.$imdbId);
        return $this->cache->get($cacheKey, function() use ($imdbId) {
            return $this->actualGateway->getMovieById($imdbId);
        });
    }
    public function searchByTitle(string $title): array
    {
        $cacheKey = sprintf('search_'.md5($title));
        return $this->cache->get($cacheKey, function(ItemInterface $item) use ($title) {
            $item->expiresAfter(self::EXPIRES_AFTER);
            return $this->actualGateway->searchByTitle($title);
        });
    }

    public function getPosterByMovie(Movie $movie): string
    {
        $cacheKey = sprintf('poster_'.md5($movie->getTitle()));
        return $this->cache->get($cacheKey, function() use ($movie) {
            return $this->actualGateway->getPosterByMovie($movie);
        });
    }

    public function getRatedByMovie(Movie $movie): string
    {
        $cacheKey = sprintf('rated_'.md5($movie->getTitle()));
        return $this->cache->get($cacheKey, function() use ($movie) {
            return $this->actualGateway->getRatedByMovie($movie);
        });
    }
}