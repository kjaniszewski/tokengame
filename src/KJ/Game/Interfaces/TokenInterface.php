<?php
declare(strict_types = 1);

namespace KJ\Game\Interfaces;

interface TokenInterface
{
    public function getX() : int;

    public function getY() : int;

    public function getState() : int;

    public function isWinning() : bool;

    public function flip();
}
