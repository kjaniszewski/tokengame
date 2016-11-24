<?php
declare(strict_types = 1);

namespace KJ\Game;

use KJ\Game\Interfaces\BoardInterface;

class TokenGame
{
    /** @var BoardInterface */
    public $board;

    /** @var \DateTime */
    protected $startTime;

    protected $timeLimitInSeconds = 60;
    protected $maxTries = 5;
    protected $tryCount = 0;

    public function __construct(BoardInterface $board)
    {
        $this->board = $board;
    }

    public function getBoard()
    {
        // TODO: write logic here
    }

    public function checkIfReadyForGame()
    {
        if ($this->board->getHeight() === 0 || $this->board->getWidth() === 0) {
            throw new \InvalidArgumentException('Board does not have size defined');
        }

        if ($this->board->getStorage()->count() !== $this->board->getHeight() * $this->board->getWidth()) {
            throw new \InvalidArgumentException('Board does not have all fields filled with tokens');
        }

        if ($this->board->getWinningTokenCount() === 0) {
            throw new \InvalidArgumentException('There is no winning token on board');
        }

        if ($this->board->getWinningTokenCount() > 1) {
            throw new \InvalidArgumentException('There can be only one winning token on board');
        }

        return true;
    }

    public function setStartTime(\DateTime $time)
    {
        $this->startTime = $time;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function checkIfTimeRanOut()
    {
        $now = new \DateTime();
        return $now->getTimestamp() - $this->startTime->getTimestamp() > $this->timeLimitInSeconds;
    }

    public function setTimeLimit(int $seconds)
    {
        $this->timeLimitInSeconds = $seconds;
    }

    public function getTimeLimit()
    {
        return $this->timeLimitInSeconds;
    }

    public function tryToken($x, $y)
    {
        if (!$this->isGameStarted()) {
            throw new \Exception('Game is not started yet.');
        }

        if ($this->checkIfTimeRanOut()) {
            $this->stopGame();
            throw new \Exception('You lost. Time out');
        }
        $this->tryCount++;

        $result = $this->board->revealAt($x, $y);

        if ($result === false && $this->tryCount === $this->maxTries) {
            $this->stopGame();
            throw new \Exception('You lost. Max tries limit reached');
        }

        if ($result === true) {
            $this->stopGame();
        }

        return $result;
    }

    public function startGame()
    {
        $this->checkIfReadyForGame();
        $this->setStartTime(new \DateTime());
        $this->tryCount = 0;
    }

    private function isGameStarted() : bool
    {
        return $this->startTime !== null;
    }

    public function stopGame()
    {
        $this->startTime = null;
        $this->tryCount = 0;
    }

    public function getTryCount() : int
    {
        return $this->tryCount;
    }
}
