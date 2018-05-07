<?php

namespace App;

class Route {

    protected $routes;
    protected $request;

    function __construct()
    {
        $this->routes = [];
        $this->request = [];
        $this->request['params'] = [];
        $this->request['method'] = $_SERVER['REQUEST_METHOD'];
        [$this->request['uri'], $query] = array_merge(explode('?', $_SERVER['REQUEST_URI'], 2), ['']);
        $params = explode('&', $query);
        foreach ($params as $p) {
            if ($p == '') { continue; }
            $x = explode('=', $p);
            $this->request['params'][$x[0]] = $x[1] ?? '';
        }
        $this->request['header'] = [
            'X-RECIPE-TOKEN' => $_SERVER['HTTP_X_RECIPE_TOKEN'] ?? "",
            'username' => $_SERVER['PHP_AUTH_USER'] ?? "",
            'password' => $_SERVER['PHP_AUTH_PW'] ?? "",
        ];
        $this->request['data'] = @json_decode(file_get_contents('php://input'));
    }

    public function add($method, $path, $func, $validater=[])
    {
        [$controller, $action] = explode('@', $func);
        ctype_upper($method);
        $path = str_replace("/", "\/", $path);
        foreach ($validater as $key => $value) {
            $path = str_replace("{".$key."}", "(".$value.")", $path);
        }
        $this->routes[] = [
            'method' => $method,
            'uri' => "/^".$path."$/",
            'controller' => $controller,
            'action' => $action,
            'validater' => $validater,
        ];
        return $this;
    }

    public function run()
    {
        foreach($this->routes as $r) {
            $matchRes = preg_match($r['uri'], $this->request['uri'], $matches);
            $controller = $r['controller'];
            $klass = "\App\Controllers\\$controller";

            if($r['method'] !== $this->request['method']) { continue; }
            if(!$matchRes) { continue; }
            if(!class_exists("\\App\\Controllers\\{$controller}")) { continue; }
            $c = new $klass;

            if (count($matches) > 1){
                $i = 1;
                foreach ($r['validater'] as $key => $value) {
                    $this->request[$key] = $matches[$i];
                    $i++;
                }
            }
            echo $c->{$r['action']}($this->request);
            return;
        }
        http_response_code(404);
    }

}
