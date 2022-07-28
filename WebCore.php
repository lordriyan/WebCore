<?php /*///////////////////////////////////////////////////////////////////////////////////////////

        WebCore is a web framework run in php to manage all web sector like Router, 
    Database, etc. This framework build in php7 in XAMPP Web Server on Windows 10

    PHP Supported       : php5 - lastest
    Database Supported  : mySQL

    Installation
        - Put this file in web root path
        - Run this script via web browser
        - Done

    Author Information
        Name        : Riyan Saputra
        Nickname    : LordRiyan / Riyan-Sama
        Website     : lordriyan.github.io

/////////////////////////////////////////////////////////////////////////////////////////////////*/
    
    const DEFAULT_CONTROLLER = "index"; // The default controller for index in controllers folder, just type filename without extension .php

    class WebCore
    {
        // Declare public variable
            public $request_uri, $root_path;
        
        // Public function
            public function __construct() // Execute when WebCore class called
            {
                $this->request_uri = $this->parse_uri();
                $this->root_path = dirname(__FILE__);

            }
            public function html_error($code) // Call html error
            {
                if (file_exists($this->root_path.'/views/error_page.php')) {
                    include_once($this->root_path.'/views/error_page.php');
                } else {
                    echo "<h1>Error ".$code."</h1>";
                }
                
            }
            public function load_model($name = "") // Call the model in models folder into controller
            {
                if (file_exists($this->root_path.'/models/'.$name.'.php')) {
                    include_once($this->root_path.'/models/'.$name.'.php');
                    return new Models();
                } else {
                    $this->html_error(403);
                }
            }
            public function load_view($name = "", $data = [])// Call the view in views folder into controller
            {
                foreach ($data as $k => $v) {${$k} = $v;}
                if (file_exists($this->root_path.'/views/'.$name.'.php')) {
                    include_once($this->root_path.'/views/'.$name.'.php');
                } else {
                    $this->html_error(403);
                }
            }

        // Private function
            private function parse_uri() // Parsing url requested by client
            {
                $uri = $_SERVER['REQUEST_URI'];
                $uri = explode("?",$uri)[0];
                $uri = explode("/",$uri);
                for ($i=0; $i < count($uri); $i++) {
                    if(strlen($uri[$i]) <= 0){
                        array_splice($uri, $i, 1);
                        $i = 0;
                    }
                }
                if ((count($uri) > 1) AND ($uri[0] == "")) array_splice($uri, 0, 1);
                if ($uri[0] == "") $uri[0] = DEFAULT_CONTROLLER;
                return $uri;
            }
    }

    // Initializing the session
        session_start();
        
    // Initializing WebCore Class
        $core = new WebCore();

    // Managing license
        $license = wordwrap(strtoupper(sha1(md5_file($core->root_path."/LICENSE"))) , 4 , '-' , true );
        $serial = wordwrap(strtoupper(sha1(md5($_SERVER['SERVER_NAME']))) , 4 , '-' , true );
        
        

    // Router algorithm
        $ctrl_path = $core->root_path.'/controllers/'.$core->request_uri[0].'.php';
        if (file_exists($ctrl_path)) // Call the controller, return error 404 if false
        {
            require_once($ctrl_path); // Call the controller
            $ctrl = new Controller(); // Call the controller class
            $func_name = (isset($core->request_uri[1])) ? $core->request_uri[1] : "index"; // Get the function name
            if(method_exists($ctrl, $func_name)) // Check if function has declare
            {
                $ctrl->$func_name(); // Execute the funtion
            } else $core->html_error(404);
        } else $core->html_error(404);