<?php

class Roomvu_Marketing_Layout
{

    protected $basePath;
    public function __construct($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * load the view file
     *
     * @param $viewName
     * @param array $data data to load view in key=>value pair
     * @return false|string
     */
    public  function render($viewName, $data){
        extract($data);
        ob_start();
        include $this->basePath . 'views/' . $viewName;
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }
}