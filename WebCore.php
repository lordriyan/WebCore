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

    date_default_timezone_set('Asia/Jakarta');

    require_once("config.php");

    class WebCore
    {
        // Declare public variable
            public $request_uri, $root_path, $db;

        // Public function
            public function __construct() // Execute when WebCore class called
            {
                $this->request_uri = $this->parse_uri();
                $this->root_path = dirname(__FILE__);   
                $this->db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
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
                    return new $name();
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
                $uri = str_replace(SUB_DIR, "", $uri);
                $uri = explode("?",$uri)[0];
                $uri = explode("/",$uri);
                $sub = explode("/", SUB_DIR);

                for ($i=0; $i < count($uri); $i++) {
                    if(strlen($uri[$i]) <= 0){
                        array_splice($uri, $i, 1);
                        $i = 0;
                    }
                }
                if ((count($uri) > 1) AND ($uri[0] == "")) array_splice($uri, 0, 1);
                if ($uri[0] == "") $uri[0] = "index";
                return $uri;
            }
    }

    // Check for initializing
    if (!is_dir("assets")) {
        mkdir("assets");
        mkdir("assets/images");
        mkdir("assets/styles");
        mkdir("assets/scripts");
        mkdir("assets/vendors");
    }
    if (!is_dir("controllers")) {
        mkdir("controllers");
        file_put_contents("controllers/index.php", "<?php\n\n\tclass Controller extends WebCore\n\t{\n\t\tpublic function index(\$v)\n\t\t{\n\t\t\tprint_r(\$v);\n\n\t\t\t\$model = \$this->load_model(\"test\");\n\n\t\t\techo \$model->model_example();\n\n\t\t\t// \$this->load_view(\"test\", array(\"var1\" => \"value\"));\n\n\t\t}\n\t}");
    }
    if (!is_dir("models")) {
        mkdir("models");
        file_put_contents("models/test.php", "<?php\n\n\tclass Test extends WebCore\n\t{\n\t\tpublic function model_example()\n\t\t{\n\t\t\t// Code goes here..\n\t\t\techo \"model loaded\";\n\t\t}\n\t}");
    }
    if (!file_exists(".htaccess")) file_put_contents(".htaccess", "<IfModule mod_rewrite.c>\n\tOptions -Indexes\n\n\tRewriteEngine On\n\tRewriteCond $1 !^(index\.php|resources|robots\.txt)\n\tRewriteCond %{REQUEST_FILENAME} !-f\n\tRewriteCond %{REQUEST_FILENAME} !-d\n\tRewriteRule ^(.+)$ index.php?/$1 [L,QSA]\n\n\tErrorDocument   403   /404\n</IfModule>");
    if (!is_dir("views")) mkdir("views");

    if (!file_exists("index.php")) {
        file_put_contents("index.php", "<?php require_once(\"WebCore.php\");");
        header("location: ".SUB_DIR."/");
    }

    // Initializing the session
        session_start();

    // Initializing WebCore Class
        $core = new WebCore();

    // Router algorithm
        $ctrl_path = $core->root_path.'/controllers/'.$core->request_uri[0].'.php';

        if (file_exists($ctrl_path)) { // Call the controller, return error 404 if false
            require_once($ctrl_path); // Call the controller
            $ctrl = new Controller(); // Call the controller class
            $func_name = (isset($core->request_uri[1])) ? $core->request_uri[1] : "index"; // Get the function name
            if(method_exists($ctrl, $func_name)) { // Check if function has declare
                $value = [];
                if (isset($core->request_uri[2])) {
                    for ($i=2; $i < count($core->request_uri); $i++) { 
                        array_push($value, $core->request_uri[$i]);
                    }
                }

                $ctrl->$func_name($value); // Execute the funtion
            } else $core->html_error(404);
        } else $core->html_error(404);