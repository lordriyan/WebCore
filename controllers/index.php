<?php

    class Controller extends WebCore
    {
        public function index()
        {
            $model = $this->load_model("test");
            echo $model->model_example();

            $this->load_view("test", ['message' => "Hello World!"]);
        }
    }
