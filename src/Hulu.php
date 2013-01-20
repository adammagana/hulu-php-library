<?php
    class HuluException extends Exception {}
    
    class Hulu {
        /* The 'Hulu' string passed to the constructor is what Hulu calls the `dp_id`.
        It is what most would call a client id. It is required by all Hulu endpoints. */
        protected $client_id;
        
        /* sprintf URL declarations */
        private $base_url =     'http://m.hulu.com/';
        private $search_url =   'http://m.hulu.com/search?dp_identifier=%s&query=%s&items_per_page=%d&page=%d';
        private $embed_code =   '<iframe src="http://www.hulu.com/embed.html?eid=%s" width="%d" height="%d" frameborder="0" scrolling="no" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>';

        /* Specific URL references
        $companies_url =        'http://m.hulu.com/companies?dp_id=hulu&limit=%d&order_by=%s';
        $genres_url =           'http://m.hulu.com/channels?dp_id=hulu&order_by=name%20asc&limit=300';
        $shows_url =            'http://m.hulu.com/shows?dp_id=hulu&limit=%d&page=%d&order_by=%s&total=%d';
        $shows_by_company_url = 'http://m.hulu.com/shows?dp_id=hulu&limit=%d&page=%d&company_id=%d&order_by=%s&total=%d';
        $shows_by_genre_url =   'http://m.hulu.com/shows?dp_id=hulu&limit=%d&page=%d&channel=%s&order_by=%s&total=%d';
        $videos_url =           'http://m.hulu.com/videos?dp_id=hulu&order_by=%s&limit=%d&page=%d&total=%d';
        $videos_by_show_url =   'http://m.hulu.com/videos?dp_id=hulu&order_by=%s&limit=%d&show_id=%s&page=%d&total=%d';
        */
        
        public function __construct($client_id='Hulu') {
            if(empty($client_id)) {
                throw new HuluException("Missing param: client_id");
            }
            $this->client_id = $client_id;
        }
        
        /**
         * @param uri   defines where the data should be retrieved from
         * @return      Hulu response data
         */
        protected function http($url) {
            $ch = curl_init($url);
            
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $response = curl_exec($ch);
            
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if($httpCode == 404) {
                throw new HuluException('Failure to retrieve Hulu content.');
            }else {
                return new SimpleXMLElement($response);
            }
        }
        
        protected function generateUrl($type='', $params=array()) {
            $url = $this->base_url.$type.'?dp_id='.$this->client_id;
            if(!empty($params)) {
                foreach($params as $key => $value) {
                    $url .= '&'.$key.'='.$value;
                }
            }
            return $url;
        }
        
        public function getCompanies($params=array()) {
            $url = $this->generateUrl('companies', $params);
            return $this->http($url);
        }
        
        public function getGenres($params=array()) {
            $url = $this->generateUrl('channels', $params);
            return $this->http($url);
        }
        
        public function getShows($params=array()) {
            $url = $this->generateUrl('shows', $params);
            return $this->http($url);
        }
        
        public function getVideos($params=array()) {
            $url = $this->generateUrl('videos', $params);
            return $this->http($url);
        }
        
        public function getEmbedCode($eid, $width = 512, $height = 288) {
            return sprintf($this->embed_code, $eid, $width, $height);
        }
        
        // Requires dp_identifier rather than dp_id
        public function search($query, $items_per_page=10, $page=1) {
            $url = sprintf($this->search_url, $this->client_id, $query, $items_per_page, $page);
            return $this->http($url);
        }
    }
?>
