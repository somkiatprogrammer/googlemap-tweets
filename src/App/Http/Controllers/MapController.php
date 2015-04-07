<?php
namespace App\Http\Controllers;

use App\Search;

class MapController extends Controller {
	
	/**
	 * Permission Guest.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->middleware ( 'guest' );
	}
	
	/**
	 * Show the application Google Map screen to the user.
	 *
	 * @return Response
	 */
	public function index() {
		$uid = session_id();
		$history = Search::where('uid', '=', $uid)
					->orderBy('updated_at', 'DESC')
					->get();
		return view ( 'map' )->with( 'history', $history );
	}
	
	/**
	 * Get Tweets by location.
	 *
	 * @return Json
	 */
	public function tweets() {
		$lat = $_GET ["lat"];
		$long = $_GET ["long"];
		$search = $_GET ["search"];
		$uid = session_id();
		$retweet = false;
		
		$s = Search::where('search', '=', $search)
					->where('uid', '=', $uid)
					->get();
		if (count($s) > 0) {
			$tweets = json_decode( $s[0] );
			if ($tweets->updated_at < date("Y-m-d H:i:s", strtotime("+1 hour"))) {
				$tweets = $tweets->data;
			} else {
				$retweet = true;
			}
		} else {
			$retweet = true;
		}
		
		if (true == $retweet) {
			$settings = array (
				'oauth_access_token' 		=> config ( 'twitter.oauth_access_token' ),
				'oauth_access_token_secret' => config ( 'twitter.oauth_access_token' ),
				'consumer_key'			 	=> config ( 'twitter.consumer_key' ),
				'consumer_secret' 			=> config ( 'twitter.consumer_secret' ) 
			);
			
			$url = 'https://api.twitter.com/1.1/search/tweets.json';
			$getfield = '?q=' . $search . '&count=' . config ( 'twitter.count' ) . '&geocode=' . $lat . ',' . $long . ',' . config ( 'twitter.distance' );
			$requestMethod = 'GET';
			
			$twitter = new \TwitterAPIExchange ( $settings );
			$tweets = $twitter->setGetfield ( $getfield )->buildOauth ( $url, $requestMethod )->performRequest ();
			if (count($s) == 0) {
				$s = new Search();
				$s->uid = $uid;
				$s->search = $search;
				$s->data = $tweets;
				$s->save();
			} else {
				Search::where('search', '=', $search)
					->where('uid', '=', $uid)
					->update(['data' => $tweets]);
			}			
		}
		echo $tweets;
	}
}
