# Blade Template in Codeigniter

This can render .blade.php in Codeinteger and can convert Strings into blade templating system.

- This library follows HMVC
- This has predefine cache for rendering files quickly
- It supports:
  > comments, echos, forelse, empty, includes, layouts, section_start, section_end, yields, yield_sections, extensions, else, unless, endunless, endforelse, structure_openings, structure_closings

### Installation

- Download the package by [clicking here](https://github.com/aididalam/Codeigniter-Blade-Template/archive/refs/tags/first.zip "clicking here")
- Extract the file to your project folder

### Initialization

```php
$this->load->library("bladeview");
```

### Methods:

```php
$this->bladeview->set('name', 'Jhon')
				->set('car_array', array("BMW", "FORD", "Mazda"))
				->append('car_array', "Volvo")
				->set_data(array('age' => 22, 'gender' => 'male'))
				->render('test', array('message' => 'Hello World!'));
```

---

### Rendaring \*blade.php

```php
$data = array(
	"foo" => "Hello",
	"bar" => "World",
);
$this->bladeview->render("test", $data);  //File extension *blade.php
```

---

### Rendaring String

```php
$string = "{{\$foo}} {{\$bar}}";
$data = array(
	"foo" => "<b>Hello</b>",
	"bar" => "World",
);
$this->bladeview->render($string, $data, false); //3rd parameter false is important
```

---

**You can also save the blade output in a variable**

```php
$View_blade_output=$this->bladeview->render("test", $data,true,true);
$String_blade_output=$this->bladeview->render($string, $data,flase,true);
```

---

### Example

```php
class Welcome extends CI_Controller {
public function __construct() {
        parent::__construct();
		$this->load->library("bladeview");
	}

	public function renderView(){
		$data=array(
				"name"=>"Jhon",
				"age"=>21
			);
		$this->bladeview->render("test", $data);
	}

	public function renderString(){
		$data=array(
				"name"=>"Jhon",
				"age"=>21
			);
		$string="Hello I'm \{{$name}}. My age is \{{$age}}";
		$this->bladeview->render($string, $data,false);
	}
}
```

For more details please follow laravel blade documentaion.
