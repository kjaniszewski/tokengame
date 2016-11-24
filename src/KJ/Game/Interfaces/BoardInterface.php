<?php
declare(strict_types = 1);

namespace KJ\Game\Interfaces;

interface BoardInterface
{
    public function setWidth(int $width);

    public function setHeight(int $height);

    public function getWidth() : int;

    public function getHeight() : int;

    public function getWinningTokenCount() : int;

    public function getTokens() : array;

    public function addToken(TokenInterface $token) : bool;

    public function getStorage() : TokenStorageInterface;

    public function revealAt(int $x, int $y) : bool;
}
