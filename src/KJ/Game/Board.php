<?php
declare(strict_types = 1);

namespace KJ\Game;


use KJ\Game\Interfaces\BoardInterface;
use KJ\Game\Interfaces\TokenInterface;
use KJ\Game\Interfaces\TokenStorageInterface;

class Board implements BoardInterface
{
    protected $width = 0;
    protected $height = 0;

    /** @var TokenStorageInterface */
    public $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function setWidth(int $width)
    {
        if ($width <= 0) {
            throw new \InvalidArgumentException('Board must have positive number of columns');
        }
        $this->width = $width;
    }

    public function setHeight(int $height)
    {
        if ($height <= 0) {
            throw new \InvalidArgumentException('Board must have positive number of rows');
        }
        $this->height = $height;
    }

    public function getWidth() : int
    {
        return $this->width;
    }

    public function getHeight() : int
    {
        return $this->height;
    }

    public function addToken(TokenInterface $token) : bool
    {
        if ($token->getX() > $this->width || $token->getY() > $this->getHeight()) {
            throw new \InvalidArgumentException('Token position is out of bounds');
        }
        $this->tokenStorage->add($token);

        return true;
    }

    public function getTokens() : array
    {
        return $this->tokenStorage->getAll();
    }

    public function getStorage() : TokenStorageInterface
    {
        return $this->tokenStorage;
    }

    public function fillUp(string $tokenClassName, $state) : int
    {
        $count = $this->getHeight() * $this->getWidth();
        $winningTokenNumber = random_int(0, $count - 1);

        $i = 0;
        for ($x = 1; $x <= $this->width; $x++) {
            for ($y = 1; $y <= $this->height; $y++) {
                $token = new $tokenClassName($x, $y, $state);
                if ($i === $winningTokenNumber) {
                    $token->setWinning(true);
                }
                $this->addToken($token);

                $i++;
            }
        }

        return $this->tokenStorage->count();
    }

    public function getWinningTokenCount() : int
    {
        $count = 0;
        /** @var TokenInterface $token */
        foreach ($this->getTokens() as $token) {
            if ($token->isWinning()) {
                $count++;
            }
        }

        return $count;
    }

    public function revealAt(int $x, int $y) : bool
    {
        $token = $this->tokenStorage->getAt($x, $y);

        if ($token->getState() == Token::SIDE_OBVERSE) {
            throw new \InvalidArgumentException('Token is alreade revealed');
        }

        return $token->isWinning();
    }

}
