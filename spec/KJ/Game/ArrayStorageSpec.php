<?php
declare(strict_types = 1);

namespace spec\KJ\Game;

use KJ\Game\Token;
use KJ\Game\ArrayStorage;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ArrayStorageSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldImplement('KJ\Game\Interfaces\TokenStorageInterface');
        $this->getAll()->shouldBeArray();
    }

    function it_can_add_token(Token $token)
    {
        $this->setDefaultTokenValues($token, 1, 1, Token::SIDE_OBVERSE);

        $this->add($token);

        $tokens = $this->getAll();
        $tokens[0]->shouldBe($token);

        $this->getAt(1, 1)->shouldBe($token);
    }

    function it_checks_if_token_at_position_exists(Token $token)
    {
        $this->setDefaultTokenValues($token, 1, 1, Token::SIDE_OBVERSE);

        $this->add($token);

        $this->shouldThrow('\InvalidArgumentException')->duringGetAt(1, 2);
    }

    function it_does_not_allow_to_adding_multiple_tokens_to_one_position(Token $token)
    {
        $this->setDefaultTokenValues($token, 1, 1, Token::SIDE_OBVERSE);

        $this->add($token);

        $this->shouldThrow('\InvalidArgumentException')->duringAdd($token);
    }

    function it_can_return_all_data(Token $token)
    {
        $this->getAll()->shouldBeArray();
        $this->getAll()->shouldHaveCount(0);

        $this->setDefaultTokenValues($token, 1, 1, Token::SIDE_OBVERSE);

        $this->add($token);
        $this->getAll()->shouldHaveCount(1);
    }

    protected function setDefaultTokenValues(Token $token, $x, $y, $state)
    {
        $token->getX()->willReturn($x);
        $token->getY()->willReturn($y);
        $token->getState()->willReturn($state);
    }
}
