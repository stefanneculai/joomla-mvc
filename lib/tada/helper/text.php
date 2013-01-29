<?php
/**
 * IXAPI MVC
 *
 * @copyright  Copyright (C) 2013 IXAPI
 */

/**
 * Text helper.
 *
 * @since  1.0
 */
class TadaHelperText
{
	/**
	 * Generates a random token.
	 *
	 * @param   number  $length  The default length of the token.
	 *
	 * @since   1.0
	 *
	 * @return  string
	 */
	public static function random_token($length = 32)
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";

		$size = strlen($chars);
		$str = '';
		for ( $i = 0; $i < $length; $i++ )
		{
			$str .= $chars[rand(0, $size - 1)];
		}

		return $str;
	}

	/**
	 * Truncate a string.
	 *
	 * @param   string   $string  The string.
	 * @param   integer  $limit   The limit to truncate.
	 * @param   string   $break   The break char.
	 * @param   string   $pad     The padding.
	 *
	 * @since   1.0
	 *
	 * @return  mixed
	 */
	public static function truncate($string, $limit, $break=".", $pad="...")
	{
		// Return with no change if string is shorter than $limit.
		if (strlen($string) <= $limit)
		{
			return $string;
		}

		// Is $break present between $limit and the end of the string?
		if (false !== ($breakpoint = strpos($string, $break, $limit)))
		{
			if ($breakpoint < strlen($string) - 1)
			{
				$string = substr($string, 0, $breakpoint) . $pad;
			}
		}

		return $string;
	}
}
