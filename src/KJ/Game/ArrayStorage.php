<?php

namespace KJ\Game;

use KJ\Game\Interfaces\TokenInterface;
use KJ\Game\Interfaces\TokenStorageInterface;

class ArrayStorage implements TokenStorageInterface
{
    /** @var TokenInterface[] */
    public $tokens = [];

    public $matrix = [];

    public function add(TokenInterface $token)
    {
        $this->tokens[] = $token;

        if (!array_key_exists($token->getX(), $this->matrix)) {
            $this->matrix[$token->getX()] = array();
        } elseif (array_key_exists($token->getY(), $this->matrix[$token->getX()])) {
            throw new \InvalidArgumentException('Token already exists at this position');
        }

        $this->matrix[$token->getX()][$token->getY()] = $token;
    }

    public function getAll() : array
    {
        return $this->tokens;
    }

    public function getAt(int $x, int $y) : TokenInterface
    {
        if (!array_key_exists($x, $this->matrix) || !array_key_exists($y, $this->matrix[$x])) {
            throw new \InvalidArgumentException('Token does not exists at this position');
        }

        return $this->matrix[$x][$y];
    }

    public function count() : int
    {
        return count($this->tokens);
    }


}
