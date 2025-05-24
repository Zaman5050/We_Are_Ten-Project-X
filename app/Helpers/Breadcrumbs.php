<?php

namespace App\Helpers;

class Breadcrumbs
{
    protected $breadcrumbs = [];

    public function add($name, $routeName = null, $params = [])
    {
        $url = tenant_route($routeName, $params);
        $this->breadcrumbs[] = ['name' => $name, 'url' => $url];
        return $this;
    }

    public function get()
    {
        return $this->breadcrumbs;
    }
}
