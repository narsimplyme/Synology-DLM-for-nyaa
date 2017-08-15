<?php
class TorrentSearchNyaa {
	private $qurl = 'http://sukebei.nyaa.si/?page=rss&term=%s';
	public function __construct() {
	}

	public function prepare($curl, $query) {
		$url = sprintf($this->qurl,urlencode($query));
		curl_setopt($curl, CURLOPT_URL, $url);

		curl_setopt($curl, CURLOPT_FAILONERROR, 1);
		curl_setopt($curl, CURLOPT_REFERER, $url);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 20);
		curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en; rv:1.9.0.4) Gecko/2008102920 AdCentriaIM/1.7 Firefox/3.0.4');
		curl_setopt($curl, CURLOPT_ENCODING, 'deflate');
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
		$regexp2 = 		"<item>.*".
					"<title>(.*)</title>.*".//title
					"<category>(.*)</category>.*".//category
					"<link>(.*)</link>.*".//torrent file
					"<guid>(.*)</guid>.*".//origin page
					"<description><!\[CDATA\[".
						"(\d+)\ seeder.*".//seeder
						"(\d+)\ leecher.*".//leecher
						"(\d+)\ download.*".//downloaded times
						"-\ (.*)\]\]></description>.*".//size
					"<pubDate>(.*)</pubDate>.*".//date
					"</item>.*";

		$count=0;
		if(preg_match_all("|$regexp2|siU", $response, $matches2, PREG_SET_ORDER)) {
			foreach($matches2 as $match2) {
				$i = 1;
				$title=html_entity_decode($match2[$i++]);
				$category=$match2[$i++];
				$download=html_entity_decode($match2[$i++]);
				$page=html_entity_decode($match2[$i++]);
				$seeds=$match2[$i++];
				$leechs=$match2[$i++];
				$hash=$count; //MUST unique for plugin->addResult
				$i++; //skip download times
				$size=$this->size_format($match2[$i++]);
				$datetime=$match2[$i++];

				$plugin->addResult($title, $download, $size, $datetime, $page, $hash, $seeds, $leechs, $category);
				$count++;
			}
		}
		return $count;
	}
}
?>
