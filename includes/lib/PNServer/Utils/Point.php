<?php
declare(strict_types=1);

namespace lib\PNServer\Utils;

/*
 * extracted required classes and functions from package
 *		spomky-labs/jose
 *		https://github.com/Spomky-Labs/Jose 
 *
 * @package PNServer
 * @version 1.0.0
 * @copyright MIT License - see the copyright below and LICENSE file for details
 */

/*
 * *********************************************************************
 * Copyright (C) 2012 Matyas Danter.
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES
 * OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 * ***********************************************************************
 */

use lib\PNServer\Utils\Math;

class Point
{
    /**
     * @var \GMP
     */
    private $x;

    /**
     * @var \GMP
     */
    private $y;

    /**
     * @var \GMP
     */
    private $order;

    /**
     * @var bool
     */
    private $infinity = false;

    /**
     * Initialize a new instance.
     *
     * @throws \RuntimeException when either the curve does not contain the given coordinates or
     *                           when order is not null and P(x, y) * order is not equal to infinity
     */
    private function __construct($x, $y, $order, $infinity = false)
    {
        $this->x = $x;
        $this->y = $y;
        $this->order = $order;
        $this->infinity = $infinity;
    }

    /**
     * @return Point
     */
    public static function create($x, $y, $order = null)
    {
        return new self($x, $y, null === $order ? \gmp_init(0, 10) : $order);
    }

    /**
     * @return Point
     */
    public static function infinity()
    {
        $zero = \gmp_init(0, 10);

        return new self($zero, $zero, $zero, true);
    }

    public function isInfinity()
    {
        return $this->infinity;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }

    /**
     * @param Point $a
     * @param Point $b
     */
    public static function cswap($a, $b, $cond)
    {
        self::cswapGMP($a->x, $b->x, $cond);
        self::cswapGMP($a->y, $b->y, $cond);
        self::cswapGMP($a->order, $b->order, $cond);
        self::cswapBoolean($a->infinity, $b->infinity, $cond);
    }

    private static function cswapBoolean(&$a, &$b, $cond)
    {
        $sa = \gmp_init((int) ($a), 10);
        $sb = \gmp_init((int) ($b), 10);

        self::cswapGMP($sa, $sb, $cond);

        $a = (bool) \gmp_strval($sa, 10);
        $b = (bool) \gmp_strval($sb, 10);
    }

    private static function cswapGMP(&$sa, &$sb, $cond)
    {
        $size = \max(\mb_strlen(\gmp_strval($sa, 2), '8bit'), \mb_strlen(\gmp_strval($sb, 2), '8bit'));
        $mask = (string) (1 - (int) ($cond));
        $mask = \str_pad('', $size, $mask, STR_PAD_LEFT);
        $mask = \gmp_init($mask, 2);
        $taA = Math::bitwiseAnd($sa, $mask);
        $taB = Math::bitwiseAnd($sb, $mask);
        $sa = Math::bitwiseXor(Math::bitwiseXor($sa, $sb), $taB);
        $sb = Math::bitwiseXor(Math::bitwiseXor($sa, $sb), $taA);
        $sa = Math::bitwiseXor(Math::bitwiseXor($sa, $sb), $taB);
    }
}
