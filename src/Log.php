<?php

/**
  *   Do not remove or alter the notices in this preamble.
  *   This software code regards ISAAC Standard Software.
  *   Copyright © 2021 ISAAC and/or its affiliates.
  *   www.isaac.nl All rights reserved. License grant and user rights and obligations
  *   according to applicable license agreement. Please contact sales@isaac.nl for
  *   questions regarding license and user rights.
  */

declare(strict_types=1);

namespace GazeHub;

use function array_map;
use function date;
use function implode;
use function is_string;
use function json_encode;
use function sprintf;

class Log
{
    /**
     * @var bool
     */
    private static $enabled = false;

    public static function enable()
    {
        self::$enabled = true;
        self::warn('IMPORTANT: Logging enabled!');
    }

    private static function printMsg(string $code, array $args)
    {
        if (self::$enabled) {
            $args = array_map(static function ($x) {
                if (!is_string($x)) {
                    return json_encode($x);
                }
                return $x;
            }, $args);

            echo sprintf("[%s] \033[%sm%s \033[0m\n", date('c'), $code, implode(' ', $args));
        }
    }

    /**
     * @param mixed $args
     */
    public static function info(...$args)
    {
        Log::printMsg('36', $args);
    }

    /**
     * @param mixed $args
     */
    public static function warn(...$args)
    {
        Log::printMsg('33', $args);
    }

    /**
     * @param mixed $args
     */
    public static function success(...$args)
    {
        Log::printMsg('32', $args);
    }

    /**
     * @param mixed $args
     */
    public static function error(...$args)
    {
        Log::printMsg('31', $args);
    }
}
