<?php
    class HuluException extends Exception {}
    
    class Hulu {
        /* Client ID used to identify the application with HULU */
        protected $client_id;
        
        /* sprintf URL declarations */
        private $base_url =             'http://m.hulu.com/';
        private $companies_url =        'http://m.hulu.com/companies?dp_id=hulu&limit=%d&order_by=%s';
        private $genres_url =           'http://m.hulu.com/channels?dp_id=hulu&order_by=name%20asc&limit=300';
        private $shows_url =            'http://m.hulu.com/shows?dp_id=hulu&limit=%d&page=%d&order_by=%s&total=%d';
        private $shows_by_company_url = 'http://m.hulu.com/shows?dp_id=hulu&limit=%d&page=%d&company_id=%d&order_by=%s&total=%d';
        private $shows_by_genre_url =   'http://m.hulu.com/shows?dp_id=hulu&limit=%d&page=%d&channel=%s&order_by=%s&total=%d';
        private $videos_url =           'http://m.hulu.com/videos?dp_id=hulu&order_by=%s&limit=%d&page=%d&total=%d';
        private $videos_by_show_url =   'http://m.hulu.com/videos?dp_id=hulu&order_by=%s&limit=%d&show_id=%s&page=%d&total=%d';
        private $search_url =           'http://m.hulu.com/search?dp_identifier=hulu&items_per_page=%d&page=%d&query=%s';
        private $player_url =           'http://hulu.com/embed/';
        
        
        /*
            http://m.hulu.com/  shows   ?dp_id=hulu  &limit=%d   &page=%d    &company_id=%d  &order_by=%s    &total=%d
        */
        
        
        public function __construct($client_id = '') {
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
                return $response;
            }
        }
        
        protected function generateUrl($type = '', $params = array()) {
            $url = $this->base_url.$type.'?dp_id='.$this->client_id;
            if(!empty($params)) {
                foreach($params as $key => $value) {
                    $url .= '&'.$key.'='.$value;
                }
            }
            return $url;
        }
        
        public function getCompanies($params = array()) {
            $url = $this->generateUrl('companies', $params);
            $response = $this->http($url);
            return $response;
        }
        
        public function getGenres($params = array()) {
            $url = $this->generateUrl('channels', $params);
            $response = $this->http($url);
            return $response;
        }
        
        public function getShows($params = array()) {
            $url = $this->generateUrl('shows', $params);
            $response = $this->http($url);
            return $response;
        }
        
        public function getVideos($params = array()) {
            $url = $this->generateUrl('videos', $params);
            $response = $this->http($url);
            return $response;
        }
        
        public function getEmbedCode() {
            //stub :)
        }
        
        // Requires dp_identifier rather than dp_id
        public function search() {
            $url = $this->generateUrl('videos', $params);
            $response = $this->http($url);
            return $response;
        }
    }
?>