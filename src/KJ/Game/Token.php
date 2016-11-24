<?php
declare(strict_types = 1);

namespace KJ\Game;

use KJ\Game\Interfaces\TokenInterface;

class Token implements TokenInterface
{
    const SIDE_OBVERSE = 1;
    const SIDE_REVERSE = 2;

    /** @var int */
    protected $x;

    /** @var int */
    protected $y;

    /** @var int */
    protected $state;

    /** @var bool */
    protected $isWinning = false;

    public function __construct(int $x, int $y, int $state)
    {
        if ($x <= 0 || $y <= 0) {
            throw new \InvalidArgumentException();
        }

        if ($state !== 1 && $state !== 2) {
            throw new \InvalidArgumentException();
        }

        $this->x = $x;
        $this->y = $y;
        $this->state = $state;
    }

    public function getX() : int
    {
        return $this->x;
    }

    public function getY() : int
    {
        return $this->y;
    }

    public function getState() : int
    {
        return $this->state;
    }

    public function flip()
    {
        $this->state === self::SIDE_REVERSE ? $this->state = self::SIDE_OBVERSE : $this->state = self::SIDE_REVERSE;
    }

    public function setWinning(bool $isWinning)
    {
        $this->isWinning = $isWinning;
    }

    public function isWinning() : bool
    {
        return $this->isWinning;
    }
}
