<?php

	class Controller extends WebCore
	{
		public function index($v)
		{
			print_r($v);

			$model = $this->load_model("test");

			echo $model->model_example();

			$this->load_view("test", array("var1" => "value"));

		}
	}