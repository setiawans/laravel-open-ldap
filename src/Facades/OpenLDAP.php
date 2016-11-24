<?php
/**
 * PHP OpenLDAP
 * 
 * @author   Toriq Setiawans <toriqbagus@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  PHP openLDAP
 */

namespace Setiawans\OpenLDAP\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * openLDAP facade class.
 */
class OpenLDAP extends Facade
{
	public static function getFacadeAccessor()
	{
		return 'Setiawans\OpenLDAP\OpenLDAP';
	}
}
