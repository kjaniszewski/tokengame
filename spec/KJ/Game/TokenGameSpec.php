<?php
declare(strict_types = 1);

namespace spec\KJ\Game;

use KJ\Game\ArrayStorage;
use KJ\Game\Board;
use KJ\Game\Interfaces\BoardInterface;
use KJ\Game\Interfaces\TokenStorageInterface;
use KJ\Game\Token;
use KJ\Game\TokenGame;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Prophet;

class TokenGameSpec extends ObjectBehavior
{
    protected $prophet;
    protected $tokens;

    function let(Board $board)
    {
        $this->beConstructedWith($board);
    }

    function it_takes_board_in_constructor(Board $board)
    {
        $this->beConstructedWith($board);
    }

    function it_checks_if_board_is_ready_for_game(Board $board, ArrayStorage $tokenStorage)
    {
        $board->getHeight()->willReturn(0);
        $board->getWidth()->willReturn(0);
        $board->getWinningTokenCount()->willReturn(0);

        $this->shouldThrow('\InvalidArgumentException')->duringCheckIfReadyForGame();

        $board->getHeight()->willReturn(0);
        $board->getWidth()->willReturn(5);

        $this->shouldThrow('\InvalidArgumentException')->duringCheckIfReadyForGame();

        $board->getHeight()->willReturn(5);
        $board->getWidth()->willReturn(0);

        $this->shouldThrow('\InvalidArgumentException')->duringCheckIfReadyForGame();

        $board->getHeight()->willReturn(5);
        $board->getWidth()->willReturn(5);
        $board->getStorage()->willReturn($tokenStorage);
        $tokenStorage->count()->willReturn(10);

        $this->shouldThrow('\InvalidArgumentException')->duringCheckIfReadyForGame();

        $board->getHeight()->willReturn(5);
        $board->getWidth()->willReturn(5);
        $tokenStorage->count()->willReturn(25);

        $this->shouldThrow('\InvalidArgumentException')->duringCheckIfReadyForGame();

        $board->getWinningTokenCount()->willReturn(3);
        $this->shouldThrow('\InvalidArgumentException')->duringCheckIfReadyForGame();

        $board->getHeight()->willReturn(5);
        $board->getWidth()->willReturn(5);
        $tokenStorage->count()->willReturn(25);
        $board->getWinningTokenCount()->willReturn(1);

        $this->checkIfReadyForGame()->shouldReturn(true);
    }

    function it_can_set_game_start_time()
    {
        $date = new \DateTime();
        $this->setStartTime($date);
        $this->getStartTime()->shouldBeEqualTo($date);
    }

    function it_can_check_if_time_ran_out()
    {
        $date = new \DateTime();
        $this->setStartTime($date);

        $this->checkIfTimeRanOut()->shouldReturn(false);

        $this->setStartTime($date->sub(new \DateInterval('PT61S')));

        $this->checkIfTimeRanOut()->shouldReturn(true);
    }

    function it_can_set_time_limit()
    {
        $this->setTimeLimit(50);

        $this->getTimeLimit()->shouldReturn(50);
    }

    function it_checks_if_try_is_out_of_bounds($board, ArrayStorage $arrayStorage)
    {
        $board->revealAt(10, 10)->willThrow(new \InvalidArgumentException());
        $this->prepareBoard($board, $arrayStorage);

        $this->startGame();
        $this->shouldThrow('\InvalidArgumentException')->duringTryToken(10, 10);
    }

    function it_checks_if_game_is_started($board, ArrayStorage $arrayStorage)
    {
        $this->prepareBoard($board, $arrayStorage);

        $this->shouldThrow('\Exception')->duringTryToken(1, 1);
    }

    function it_can_stop_game($board, ArrayStorage $arrayStorage)
    {
        $this->prepareBoard($board, $arrayStorage);

        $this->startGame();
        $this->stopGame();
        
        $this->getStartTime()->shouldBe(null);
        $this->getTryCount()->shouldBe(0);
    }

    function it_can_play_successfull_game($board, ArrayStorage $arrayStorage)
    {
        $this->prepareBoard($board, $arrayStorage);

        $this->startGame();
        $this->tryToken(1, 2)->shouldReturn(false);
        $this->tryToken(1, 3)->shouldReturn(false);
        $this->tryToken(1, 4)->shouldReturn(false);
        $this->tryToken(5, 3)->shouldReturn(false);
        $this->tryToken(1, 1)->shouldReturn(true);
    }

    function it_checks_for_max_tries($board, ArrayStorage $arrayStorage)
    {
        $this->prepareBoard($board, $arrayStorage);

        $this->startGame();
        $this->tryToken(1, 2)->shouldReturn(false);
        $this->tryToken(1, 3)->shouldReturn(false);
        $this->tryToken(1, 4)->shouldReturn(false);
        $this->tryToken(5, 3)->shouldReturn(false);
        $this->shouldThrow('\Exception')->duringTryToken(3, 1);
    }

    function it_checks_for_timeout($board, ArrayStorage $arrayStorage)
    {
        $this->prepareBoard($board, $arrayStorage);

        $this->startGame();
        $this->tryToken(1, 2)->shouldReturn(false);
        $this->setStartTime((new \DateTime())->sub(new \DateInterval('PT65S')));
        $this->shouldThrow('\Exception')->duringTryToken(3, 1);
    }

    function it_checks_for_revealing_the_same_token($board, ArrayStorage $arrayStorage)
    {
        $this->prepareBoard($board, $arrayStorage);

        $this->startGame();
        $this->tryToken(1, 2)->shouldReturn(false);
        $board->revealAt(1, 2)->willThrow(new \InvalidArgumentException());
        $this->shouldThrow('\Exception')->duringTryToken(1, 2);
    }

    /**
     * @param $board
     * @param ArrayStorage $arrayStorage
     */
    protected function prepareBoard($board, ArrayStorage $arrayStorage)
    {
        $board->getHeight()->willReturn(4);
        $board->getWidth()->willReturn(5);
        $board->getStorage()->willReturn($arrayStorage);
        $board->getWinningTokenCount()->willReturn(1);
        $arrayStorage->count()->willReturn(20);
        $i = 0;
        for ($x = 1; $x <= 5; $x++) {
            for ($y = 1; $y <= 4; $y++) {
                /** @var Token $token */
                $isWinning = $i === 0;
                $board->revealAt($x, $y)->willReturn($isWinning);
                $i++;
            }
        }
    }
}
