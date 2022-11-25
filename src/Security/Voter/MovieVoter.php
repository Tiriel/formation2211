<?php

namespace App\Security\Voter;

use App\Entity\Movie;
use App\Entity\User;
use App\Omdb\OmdbGateway;
use App\Repository\MovieRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use function PHPUnit\Framework\assertInstanceOf;

class MovieVoter extends Voter
{
    public const MOVIE = 'movie';
    public function __construct(
        private readonly AuthorizationCheckerInterface $checker,
        private readonly OmdbGateway $gateway,
        private readonly MovieRepository $repository
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Movie && $attribute === self::MOVIE;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if ($this->checker->isGranted('ROLE_ADMIN')) {
            return true;
        }
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }
        $age = !$user->getBirthday() instanceof \DateTimeImmutable
            ?: $user->getBirthday()->diff(new \DateTimeImmutable())->y;

        assert($subject instanceof Movie);
        if (!$subject->getRated()) {
            $subject->setRated($this->gateway->getRatedByMovie($subject));
            $this->repository->save($subject, true);
        }

        return match ($subject->getRated()) {
            'G', 'Not Rated' => true,
            'PG', 'PG-13' => is_int($age) && $age >= 13,
            'R', 'NC-17' => is_int($age) && $age >= 17,
            default => false,
        };
    }
}