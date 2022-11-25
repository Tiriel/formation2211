<?php
declare(strict_types=1);

namespace App\Omdb;

use App\Entity\Movie;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsDecorator(OmdbGateway::class, priority: 100)]
class LoggableOmdbGateway extends OmdbGateway
{
    public function __construct(
        private OmdbGateway $actualGateway,
        private LoggerInterface $logger,
    )
    {
    }

    public function getMovieById(string $imdbId): array
    {
        $this->logger->info('getMovieById was called');
        return $this->actualGateway->getMovieById($imdbId);
    }
    public function searchByTitle(string $title): array
    {
        $this->logger->info('searchByTitle was called');
        return $this->actualGateway->searchByTitle($title);
    }
    public function getPosterByMovie(Movie $movie): string
    {
        $this->logger->info('getMovieById was called');
        return $this->actualGateway->getPosterByMovie($movie);
    }
    public function getRatedByMovie(Movie $movie): string
    {
        $this->logger->info('getRatedByMovie was called');
        return $this->actualGateway->getRatedByMovie($movie);
    }
}