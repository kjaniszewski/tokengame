<?php
declare(strict_types = 1);

namespace spec\KJ\Game;

use KJ\Game\Token;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TokenSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(1, 1, Token::SIDE_OBVERSE);
    }

    function it_is_initializable()
    {
        $this->shouldImplement('KJ\Game\Interfaces\TokenInterface');
    }

    function it_can_set_position_and_state()
    {
        $this->beConstructedWith(1, 1, Token::SIDE_OBVERSE);

        $this->getX()->shouldReturn(1);
        $this->getY()->shouldReturn(1);
        $this->getState()->shouldReturn(Token::SIDE_OBVERSE);
    }

    function it_can_have_only_positive_positions()
    {
        $this->beConstructedWith(-1, 1, Token::SIDE_OBVERSE);
        $this->shouldThrow('\InvalidArgumentException')->duringInstantiation();

        $this->beConstructedWith(1, -1, Token::SIDE_OBVERSE);
        $this->shouldThrow('\InvalidArgumentException')->duringInstantiation();
    }

    function it_can_have_only_valid_sides()
    {
        $this->beConstructedWith(1, 1, 3);
        $this->shouldThrow('\InvalidArgumentException')->duringInstantiation();
    }
    
    function it_can_be_flipped()
    {
        $this->beConstructedWith(1, 1, Token::SIDE_OBVERSE);
        $this->flip();
        $this->getState()->shouldReturn(Token::SIDE_REVERSE);
    }

    function it_can_be_winning_token()
    {
        $this->setWinning(1);
        $this->isWinning()->shouldReturn(true);
    }
}
