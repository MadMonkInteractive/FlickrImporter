<?php

/*
* This file pulls in flickr collections, sets, and photos
* This assumes that the following hierarchy is used
* - Collections
* --> Sets
*  --> Photos
* @author Scott Dover <scott@madmonkinteractive.com>
* @since Fri Aug 10 08:34:25 EDT 2012
*/

include '../wp-config.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

define('FLICKR_KEY', 'FLICKR_KEY');
define('FLICKR_SECRET', 'FLICKR_SECRET');
define('USER_ID', 'USER_ID');

mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysql_select_db(DB_NAME);


class Flickr {

	public static function call($method, $payload = array()) {
		
		$params = array(
			'api_key' => FLICKR_KEY,
			'user_id' => USER_ID,
			'format' => 'php_serial'
		);
		
		$params = array_merge($params, $payload);
		$params = array_merge($params, array('method' => $method));
		
		$curl = curl_init('http://api.flickr.com/services/rest');
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		curl_close($curl);
		
		$data = unserialize($response);
		return $data;
	}
}


/* Pull in all photos */
$page = 1;
$query_vars = array();
do {
	/* Call page 1 */
	$photos = Flickr::call('flickr.photos.search', array(
		'per_page' => 500,
		'page' => $page
	));
	/* Store each photo */
	foreach ($photos['photos']['photo'] as $photo) {
		$photo = (object) $photo;
		$item = (object) array(
			'flickr_id' => $photo->id,
			'title' => $photo->title,
			'thumbnail' => "http://farm{$photo->farm}.staticflickr.com/{$photo->server}/{$photo->id}_{$photo->secret}_n.jpg",
			'large' => "http://farm{$photo->farm}.staticflickr.com/{$photo->server}/{$photo->id}_{$photo->secret}_c.jpg",
		);

		$title = addslashes(str_replace("'", '&#39;', $item->title));
		$query_vars[] = "('{$item->flickr_id}', '{$title}', '{$item->thumbnail}','{$item->large}')";
	}
	
	$page += 1;
	
} while ($page <= $photos['photos']['pages']);

/* Perform query to insert all photos */
$query = 'INSERT INTO photo (flickr_id, title, thumbnail, large) VALUES '.implode(',',$query_vars);
mysql_query('DELETE FROM photo');
mysql_query($query);


/* Pull in albums */
$sets = Flickr::call('flickr.photosets.getList');
$set_vars = array();
$photo_set_vars = array();
foreach ($sets['photosets']['photoset'] as $set) {
	$set = (object)$set;
	// Grab the info for the set
	$title = addslashes(str_replace("'", '&#39;',$set->title['_content']));
	$set_vars[] = "('{$set->id}','{$title}')";
	
	$photos = Flickr::call('flickr.photosets.getPhotos', array(
		'per_page' => 500,
		'photoset_id' => $set->id
	));
	foreach ($photos['photoset']['photo'] as $photo) {
		$photo = (object) $photo;
		$photo_set_vars[] = "('{$photo->id}', '{$set->id}')";
	}
}

/* Insert sets */
mysql_query('DELETE FROM album');
mysql_query("INSERT INTO album (flickr_id, title) VALUES ".implode(',',$set_vars));
echo "INSERT INTO set (flickr_id, title) VALUES ".implode(',',$set_vars);


/* Insert set / photo relationship */
mysql_query('DELETE FROM photo_album');
mysql_query('INSERT INTO photo_album (photo_id, album_id) VALUES '.implode(',', $photo_set_vars));

$collections = Flickr::call('flickr.collections.getTree');
$collection_vars = array();
$collection_album_vars = array();
foreach ($collections['collections']['collection'] as $collection) {
	$collection = (object) $collection;
	$title = addslashes(str_replace("'", '&#39;', $collection->title));
	$collection_vars[] = "('{$collection->id}',  '{$title}')";
	foreach ($collection->set as $set) {
		$set = (object)$set;
		$collection_album_vars[] = "('{$set->id}', '{$collection->id}')";
	}
}

mysql_query("DELETE FROM collection");
mysql_query("INSERT INTO collection (flickr_id,title) VALUES ".implode(',', $collection_vars));

mysql_query("DELETE FROM album_collection");
mysql_query("INSERT INTO album_collection (album_id, collection_id) VALUES ".implode(',', $collection_album_vars));


?>