<?php

namespace App\Command;

use App\Entity\Movie;
use App\Omdb\OmdbGateway;
use App\Repository\MovieRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:omdb:query',
    description: 'Search for a movie in the OMDb Api.',
)]
class OmdbQueryCommand extends Command
{

    public function __construct(
        private OmdbGateway $omdbGateway,
        private MovieRepository $movieRepository,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('title', InputArgument::REQUIRED, 'The title of the movie')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $title = $input->getArgument('title');

        $movieCollection = $this->omdbGateway->searchByTitle($title);
        $helper = $this->getHelper('question');

        /**
        $movieCollection = [
            ['title' => 'toto', 'description' => 'tata'],
            ['title' => 'Foobar', 'description' => 'some description'],
        ]
         */

        $choiceOptions = array_map(fn(array $movie) => $movie['Title'], $movieCollection);
        
        $question = new ChoiceQuestion(
            'Please choose from the following list of movies.',
            // choices can also be PHP objects that implement __toString() method
            $choiceOptions,
        );
        $question->setErrorMessage('Movie %s is invalid.');

        $chosenMovieTitle = $helper->ask($input, $output, $question); // === "Foobar"

        $chosenMovie = array_values(array_filter(
            $movieCollection,
            fn(array $movie) => $movie['Title'] === $chosenMovieTitle
        ))[0];

        $chosenMovie = $this->omdbGateway->getMovieById($chosenMovie['imdbID']);

        $movie = new Movie();
        $movie->setTitle($chosenMovie['Title']);
        $movie->setDescription($chosenMovie['Plot']);
        $movie->setReleaseDate(new \DateTime($chosenMovie['Released']));

        $this->movieRepository->save($movie, true);

        $io->success('Movie '.$chosenMovieTitle.' has been persisted.');
        return Command::SUCCESS;
    }
}
