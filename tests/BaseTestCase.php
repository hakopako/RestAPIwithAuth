<?php

use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    protected function setUp()
    {
        $this->uri = '';
        $this->method = 'GET';
    }

    protected function sendRequest($data=null, $header=[])
    {
        $opts = [
            'http' => [
                'method' => $this->method,
                'header' => implode("\r\n", $header)."\r\n",
                'content' => $data,
            ]
        ];
        $context = stream_context_create($opts);
        try {
            $file = file_get_contents($this->uri, false, $context);
            return [$this->parseHeaders($http_response_header), $file];
        } catch(\Exception $e) {
            $this->assertTrue(false, 'HTTP Request Error.');
        }
    }

    protected function parseHeaders($headers=[])
    {
        $head = [];
        foreach($headers as $k => $v){
            $t = explode(':', $v, 2);
            if(isset($t[1])) {
                $head[trim($t[0])] = trim($t[1]);
            } else {
                $head[] = $v;
                if(preg_match( "/^HTTP\/[0-9\.]+\s+([0-9]+)\s/",$v, $out)){
                    $head['reponse_code'] = intval($out[1]);
                }
            }
        }
        return $head;
    }

}
