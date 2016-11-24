<?php
declare(strict_types = 1);

namespace KJ\Game\Interfaces;

interface TokenStorageInterface
{
    public function add(TokenInterface $token);

    public function getAll() : array;

    public function getAt(int $x, int $y) : TokenInterface;

    public function count() : int;
}
