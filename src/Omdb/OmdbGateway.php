<?php
declare(strict_types=1);

namespace App\Omdb;

use App\Entity\Movie;
use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OmdbGateway
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $apiKey
    )
    {
    }

    private const OMDB_QUERY_FORMAT = 'https://www.omdbapi.com/?apikey=%s&t=%s';

    public function getRatedByMovie(Movie $movie): string
    {
        $omdbQueryUrl = sprintf(
            self::OMDB_QUERY_FORMAT,
            $this->apiKey,
            $movie->getTitle()
        );

        $json = $this->httpClient->request('GET', $omdbQueryUrl)->toArray();

        return $json['Rated'] ?? '';
    }

    public function getPosterByMovie(Movie $movie): string
    {
        $omdbQueryUrl = sprintf(
            self::OMDB_QUERY_FORMAT,
            $this->apiKey,
            $movie->getTitle()
        );

        $json = $this->httpClient->request('GET', $omdbQueryUrl)->toArray();

        return $json['Poster'] ?? '';
    }

    public function searchByTitle(string $title): array
    {
        $omdbQueryUrl = sprintf(
            'https://www.omdbapi.com/?apikey=%s&s=%s',
            $this->apiKey,
            $title
        );

        $json = $this->httpClient->request('GET', $omdbQueryUrl)->toArray();

        return $json['Search'] ?? [];
    }

    public function getMovieById(string $imdbId): array
    {

        $omdbQueryUrl = sprintf(
            'https://www.omdbapi.com/?apikey=%s&i=%s',
            $this->apiKey,
            $imdbId
        );

        $json = $this->httpClient->request('GET', $omdbQueryUrl)->toArray();

        // At this point, the ImdbId should be known
        if(isset($json['Error'])) {
            throw new Exception($json['Error']);
        }

        return $json;
    }
}