<?php
/**
 * This file is part of SmartWork.
 *
 * SmartWork is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SmartWork is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SmartWork.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    SmartWork
 * @subpackage plugins
 * @author     Marian Pollzien <map@wafriv.de>
 * @copyright  (c) 2015, Marian Pollzien
 * @license    https://www.gnu.org/licenses/lgpl.html LGPLv3
 */

/**
 * Smarty string_format modifier plugin
 *
 * Type:     modifier<br>
 * Name:     number_format<br>
 * Purpose:  format numbers with number_format
 *
 * @author friend8 <map@wafriv.de>
 * @param number $number input number
 * @param int $decimals number of decimals
 * @param string $decimal decimal point
 * @param string $thousands thousands separator
 * @return string formatted string
 */
function smarty_modifier_number_format($number, $decimals = 2, $decimal = '.', $thousands = ',')
{
    return number_format($number, $decimals, $decimal, $thousands);
}