<?php

class Epost {

    // API Key
    var $regKey = '';

    // Search Text
    var $search_text = '';

    // Current page
    var $page = 1;

    // limit per page
    var $limit = 50;

    public static function new() {
        $new = new Epost(); return $new;
    }

    public function setSearch($search) {
		$search = iconv("UTF-8", "EUC-KR", str_replace(' ', '', $search) );
        $this->search_text = $search; return $this;
    }

    public function setOffset($offset) {
        $this->page = ($offset/$this->limit) + 1; return $this;
    }

    public function getResult() {
		$content  = file_get_contents($this->getUrl());
		$xml      = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
		$xmlJson  = json_encode($xml);
		$xmlArr   = json_decode($xmlJson, 1);
        $return   = array('list' => array(), 'total_count' => 0, 'limit' => $this->limit, 'page' => $this->page );

		if (isset($xmlArr['itemlist'])) {

            if ( isset($xmlArr['itemlist']['item'][0]) && is_array($xmlArr['itemlist']['item'][0]) ) {
                foreach($xmlArr['itemlist']['item'] as $item) {
                    $o = new stdClass();
                    $o->postal_code = $item['postcd'];
                    $o->old_address = $item['addrjibun'];
                    $o->new_address = $item['address'];
                    array_push($return['list'], $o);
        		}
            } else {
                $o = new stdClass();
                $o->postal_code = $xmlArr['itemlist']['item']['postcd'];
                $o->old_address = $xmlArr['itemlist']['item']['addrjibun'];
                $o->new_address = $xmlArr['itemlist']['item']['address'];
                array_push($return['list'], $o);
            }
		}

        if (isset($xmlArr['pageinfo'])) {
            $pageInfo = $xmlArr['pageinfo'];
            if (isset($pageInfo['totalCount'])) {
                $return['total_count'] = $pageInfo['totalCount'];
            }
        }

        return $return;
    }

    public function getUrl() {
        $query = array();
        $query['regkey']        = $this->regKey;
        $query['target']        = 'postNew';
        $query['countPerPage']  = $this->limit;
        $query['currentPage']   = $this->page;
        $query['query']         = $this->search_text;
        $url = 'http://biz.epost.go.kr/KpostPortal/openapi?'.http_build_query($query);
        return $url;
    }
}
