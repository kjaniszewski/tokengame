<?php
declare(strict_types = 1);

namespace spec\KJ\Game;

use KJ\Game\Board;
use KJ\Game\Interfaces\TokenStorageInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use KJ\Game\ArrayStorage;
use KJ\Game\Token;

class BoardSpec extends ObjectBehavior
{
    function let(ArrayStorage $tokenStorage)
    {
        $this->beConstructedWith($tokenStorage);
        $this->setWidth(5);
        $this->setHeight(5);
    }

    function it_is_initializable()
    {
        $this->shouldImplement('KJ\Game\Interfaces\BoardInterface');
    }

    function it_can_set_dimensions()
    {
        $this->getWidth()->shouldEqual(5);
        $this->getHeight()->shouldEqual(5);
    }

    function it_allows_only_positive_dimensions()
    {
        $this->shouldThrow('\InvalidArgumentException')->duringSetWidth(-1);
        $this->shouldThrow('\InvalidArgumentException')->duringSetHeight(-1);
    }

    function it_can_add_token_to_board(Token $token, $tokenStorage)
    {
        $this->setDefaultTokenValues($token, 1, 1, Token::SIDE_REVERSE);

        $tokenStorage->add($token)->shouldBeCalled();
        $tokenStorage->getAll()->shouldBeCalled()->willReturn(array($token));

        $this->addToken($token);

        $tokens = $this->getTokens();
        $tokens[0]->shouldBe($token);
    }

    function it_checks_if_token_position_fits_board(Token $token)
    {
        $this->setDefaultTokenValues($token, 1, 1, Token::SIDE_REVERSE);

        $this->addToken($token)->shouldReturn(true);

        $this->setDefaultTokenValues($token, 6, 5, Token::SIDE_REVERSE);
        $this->shouldThrow('\InvalidArgumentException')->duringAddToken($token);

        $this->setDefaultTokenValues($token, 5, 6, Token::SIDE_REVERSE);
        $this->shouldThrow('\InvalidArgumentException')->duringAddToken($token);
    }

    function it_can_fill_up_with_tokens($tokenStorage)
    {
        $tokenStorage->count()->willReturn(25);
        $tokenStorage->add(Argument::any())->shouldBeCalled();
        $this->fillUp('KJ\Game\Token', Token::SIDE_REVERSE)->shouldReturn(25);
    }
    
    function it_can_check_for_winning_token_existence(Token $token1, Token $token2, ArrayStorage $tokenStorage)
    {
        $token1->isWinning()->willReturn(true);
        $token2->isWinning()->willReturn(false);
        $tokenStorage->getAll()->willReturn([$token1, $token2]);

        $this->getWinningTokenCount()->shouldReturn(1);
    }
    
    function it_can_reveal_token(Token $token, Token $token2, ArrayStorage $tokenStorage)
    {
        $this->setDefaultTokenValues($token, 1, 1, Token::SIDE_REVERSE);
        $this->setDefaultTokenValues($token2, 1, 2, Token::SIDE_REVERSE);
        $tokenStorage->getAt(1,1)->willReturn($token);
        $tokenStorage->getAt(1,2)->willReturn($token2);

        $token->isWinning()->willReturn(false);
        $token2->isWinning()->willReturn(true);

        $this->revealAt(1,1)->shouldReturn(false);

        $this->revealAt(1,2)->shouldReturn(true);

        $token->getState()->willReturn(Token::SIDE_OBVERSE);

        $this->shouldThrow('\InvalidArgumentException')->duringRevealAt(1,1);
    }

    protected function setDefaultTokenValues(Token $token, $x, $y, $state)
    {
        $token->getX()->willReturn($x);
        $token->getY()->willReturn($y);
        $token->getState()->willReturn($state);
    }
}
