# Blade Template in Codeigniter

This can render .blade.php in Codeinteger and can convert Strings into blade templating system.

- This library follows HMVC
- This has predefine cache for rendering files quickly
- It supports:
  > comments, echos, forelse, empty, includes, layouts, section_start, section_end, section, sectionend, extends, yields, yield_sections, extensions, else, unless, endunless, endforelse, structure_openings, structure_closings

### Installation
```
composer require aidid/ci_blade
```

### Initialization

```php
use \Aidid\BladeView\BladeView;
	public $bladeview;
	public function __construct() {
		parent::__construct();
		$this->bladeview = new BladeView();
	}
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

### Making  blade template

```php
<div>
    @include('template-example/header')
</div>
<div>
    @yield('content')
</div>
<div>
    @include('template-example/footer')
</div>

```

```php
@extends('template-example/baseview')
@section('content')
    i am the home page
@endsection

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
use \Aidid\BladeView\BladeView;

class Example extends CI_Controller {
	public $bladeview;
	public function __construct() {
		parent::__construct();
		$this->bladeview = new BladeView();
	}

	public function index() {
		$this->bladeview->render('templated-view'); //check file under view folder
	}

	public function viewWithVariable() {
		$foo = "Foo";
		$bar = "Bar";
		$this->bladeview->render('view-with-variable', compact('foo', 'bar'));
	}

	public function directRender() {
		$string = "{{\$foo}} {{\$bar}}";
		$data = array(
			"foo" => "<b>Hello</b>",
			"bar" => "World",
		);
		$this->bladeview->render($string, $data, false); //3rd parameter false is important
	}
}
```

Check this [controller](https://github.com/aididalam/Codeigniter-Blade-Template/blob/main/application/controllers/Example.php "controller") for better understand of BladeView's function.
Check this [View Folder](https://github.com/aididalam/Codeigniter-Blade-Template/tree/main/application/views "View Folder") for better understand of view structure.

For more details please follow laravel blade documentaion.
