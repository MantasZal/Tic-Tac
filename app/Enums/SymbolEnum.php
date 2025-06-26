<?php

namespace App\Enums;

enum SymbolEnum: string
{
    case X = 'X';
    case O = 'O';

    public function opposite(): self
    {
        return $this === self::X ? self::O : self::X;
    }
}
