<?php
class TorrentSearchNyaa {
	private $qurl = 'https://nyaa.si/?page=rss&term=';
	public function __construct() {
	}

	public function prepare($curl, $query) {
		$url = $this->qurl . urlencode($query);
		curl_setopt($curl, CURLOPT_URL, $url);
	}

	public function size_format($sizestr) {
		$size_map=array(
			"KiB" => 1024,
			"MiB" => 1048576,
			"GiB" => 1073741824,
			);
		foreach ($size_map as $n => $mux) {
			if( strstr($sizestr,$n) ){
				$sizestr=floatval($sizestr)*$mux;
				break;
			}
		}
		return $sizestr;
	}

	public function parse($plugin, $response) {
		$simpleXMLobj = preg_replace("/nyaa:/", "", $response);
		$xml = simplexml_load_string($simpleXMLobj);
		$count = 0;
		foreach($xml->channel->item as $child)
		  {
			  $title=(string)$child->title;
				$download=(string)$child->link;
				$size=$this->size_format((string)$child->size);
				$datetime=(string)$child->pubDate;
				$page=(string)$child->guid;
				$hash=(string)$child->infoHash;
				$seeds=(int)$child->seeders;
				$leechs=(int)$child->leechers;
				$category=(string)$child->category;
				$count++;
				$plugin->addResult($title, $download, $size, $datetime, $page, $hash, $seeds, $leechs, $category);
		  }
			return $count;
	}
}
?>
