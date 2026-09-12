<?php

namespace App\Enums;

enum ModelsEnum: string
{
    case BIG_PICKLE_FREE = 'big-pickle';
    case DEEPSEEK_V4_FLASH_FREE = 'deepseek-v4-flash-free';
    case MIMO_V2_5_FREE = 'mimo-v2.5-free';
    case LAGUNA_S_2_1_FREE = 'laguna-s-2.1-free';
    case LING_3_0_FLASH_FREE = 'ling-3.0-flash-free';
    case NORTH_MINI_CODE_FREE = 'north-mini-code-free';
    case NEMOTRON_3_ULTRA_FREE = 'nemotron-3-ultra-free';

    public function labels(): string
    {
        return match ($this) {
            self::BIG_PICKLE_FREE => 'Big Pickle',
            self::DEEPSEEK_V4_FLASH_FREE => 'DeepSeek V4 Flash',
            self::MIMO_V2_5_FREE => 'MiMo V2.5',
            self::LAGUNA_S_2_1_FREE => 'Laguna S 2.1',
            self::LING_3_0_FLASH_FREE => 'Ling 3.0 Flash',
            self::NORTH_MINI_CODE_FREE => 'North Mini Code',
            self::NEMOTRON_3_ULTRA_FREE => 'Nemotron 3 Ultra',

        };
    }
}
