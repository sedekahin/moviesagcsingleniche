<?php
class Image_Tag extends H2o_Node {
    var $term, $cacheKey;

    function __construct($argstring, $parser, $pos=0) {
        list($this->term, $this->hack) = explode(' ', $argstring);
    }
    
    function get_api_url($term = 'hello world', $hack = ""){    	
		$term .= " ".$hack ;
    	$term = urlencode($term);
    	return "http://www.bing.com/images/search?q=$term&format=xml&qft=+filterui:imagesize-medium";
    }
      
   function fetch($context,$url) {
		$this->url = $url;
   		$feed = @file_get_contents($this->url);
        $feed = @simplexml_load_string($feed);
		return $feed;
    }

    function render($context, $stream) {
        $cache = h2o_cache($context->options);
        $term  = $context->resolve(':term');
        $hack  = $context->resolve(':hack');
        
        $url   = $this->get_api_url($term, $hack);
        $feed  = @$this->fetch($context,$url)->xpath('/searchresult/section/documentset/document');
	
        $context->set("images", $feed);
	}
}
h2o::addTag('image');