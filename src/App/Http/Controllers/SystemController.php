<?php
namespace App\Http\Controllers;

class SystemController extends Controller {
	
	/**
	 * Permission Guest.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->middleware ( 'guest' );
	}
	
	/**
	 * Create a Table in Database.
	 *
	 * @return Response
	 */
	public function install() {
		$sql = "CREATE TABLE IF NOT EXISTS `searches` (
			`uid` varchar(64) NOT NULL,
			`search` varchar(64) NOT NULL,
			`data` text NOT NULL,
			`datetime` datetime NOT NULL,
			KEY `uid` (`uid`)
			) ENGINE=MyISAM";
		if (\DB::statement($sql) ) {
			echo "<p>Create Table: OK.</p>";
		} else {
			echo "<p>Create Table: Fail.</p>";
		}
	}
}