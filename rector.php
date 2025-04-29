<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/Block',
        __DIR__ . '/Controller',
        __DIR__ . '/Helper',
        __DIR__ . '/Model',
        __DIR__ . '/Observer',
        __DIR__ . '/Plugin',
        __DIR__ . '/Setup',
        __DIR__ . '/Ui',
        __DIR__ . '/view',
    ]);

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_81,
        SetList::PHP_81,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::TYPE_DECLARATION,
    ]);

    $rectorConfig->importNames();
    $rectorConfig->parallel();
}; 