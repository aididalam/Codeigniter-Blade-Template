<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Same as laravel Blade Template
 * 
 *
 * @author      Aidid Alam
 * @package     Blade
 * @category    Libraries
 * @version     1.0.0
 * @url         https://github.com/aididalam/CI-BladeView
 *
 */
class BladeView {

    /**
     * Blade Compiler Methods
     *
     * @var array
     */
    protected $_compilers = array(
        'comments',
        'echos',
        'forelse',
        'empty',
        'includes',
        'layouts',
        'section_start',
        'section_end',
        'yields',
        'yield_sections',
        'extensions',
        'else',
        'unless',
        'endunless',
        'endforelse',
        'structure_openings',
        'structure_closings',
    );

    protected $_last_section = array();

    protected $_sections = array();

    protected $_extensions = array();

    protected $_data = array();

    public $blade_extension  = '.blade.php';

    public $cache_time = 3600;

    private $type = "view";


    public function __set($name, $value) {
        $this->_data[$name] = $value;
    }


    public function __unset($name) {
        unset($this->_data[$name]);
    }


    public function __get($name) {
        if (key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }

        $_CI = &get_instance();
        return $_CI->$name;
    }


    public function __construct() {
        $this->load->driver('cache');
    }


    /**
     * Sets global data for template
     *
     * @param string $name
     * @param mixed $value
     * @return Blade
     */
    public function set($name, $value) {
        $this->_data[$name] = $value;
        return $this;
    }


    /**
     * Append data.
     * set("foo","bar")->append("foo","-hello")
     * 
     * Output {{$foo}} = bar-hello
     *
     * @param string $name
     * @param mixed $value
     * @return Blade
     */
    public function append($name, $value) {
        if (is_array($this->_data[$name])) {
            $this->_data[$name][] = $value;
        } else {
            $this->_data[$name] .= $value;
        }

        return $this;
    }


    /**
     * Sets multiple global data in array format
     *
     * @param array $data
     * @return Blade
     */
    public function set_data($data) {
        $this->_data = array_merge($this->_data, $data);
        return $this;
    }


    /**
     * Adds a custom compiler function
     *
     * @param mixed $compiler
     * @return Blade
     */
    public function extend($compiler) {
        $this->_extensions[] = $compiler;
        return $this;
    }


    /**
     * Outputs template content. You can also pass an array of global data.
     * If $return is TRUE then returns the template as a string.
     *
     * @param string $template
     * @param array $data
     * @param bool $isView
     * @param bool $return
     * @return string
     */
    public function render($template, $data = NULL, $isView = TRUE, $return = FALSE) {
        if ($isView) {
            $this->type = "view";
        } else {
            $this->type = "string";
        }
        if (isset($data)) {
            if (is_object($data)) {
                $this->set_data(json_decode(json_encode($data), true));
            } else {
                $this->set_data($data);
            }
        }

        // Compile and run template
        $compiled = $this->_compile($template);
        $content = $this->_run($compiled, $this->_data);

        if (!$return) {
            $this->output->append_output($content);
        }

        return $content;
    }


    /**
     * Find the full path to a view file. Shows an error if file not found.
     *
     * @param string $view
     * @return string
     */
    protected function _find_view($view) {
        // Default location
        $full_path = APPPATH . 'views/' . $view . $this->blade_extension;

        // Modular Separation / Modular Extensions has been detected
        if (method_exists($this->router, 'fetch_module')) {
            $module = $this->router->fetch_module();
            list($path, $_view) = Modules::find($view . $this->blade_extension, $module, 'views/');

            if ($path) {
                $full_path = $path . $_view;
            }
        }

        // File not found
        if (!is_file($full_path) && $this->type == "view") {
            show_error('[Blade] Unable to find view: ' . $view . "<br>If you want to render String as Blade then pass 3rd parameter false (\$String, \$Render_Data, false)
            <br>
            Documentations: <a href='https://github.com/aididalam/CI-BladeView'>Github</a>");
        }

        return $full_path;
    }

    protected function _compile($template) {
        $view_path = $this->_find_view($template);
        $cache_id = 'bladeView/' . md5($view_path);
        if ($this->type == "string") {
            $cache_id = "bladeView/stringToBlade";
        }
        if ($compiled = $this->cache->file->get($cache_id)) {
            if (ENVIRONMENT == 'production') {
                return $compiled;
            }

            if ($this->type == "view") {
                $meta = $this->cache->file->get_metadata($cache_id);
                if ($meta['mtime'] > filemtime($view_path)) {
                    return $compiled;
                }
            }
        }

        // Template content
        if ($this->type == "view") {
            $template = file_get_contents($view_path);
        }



        // Compilers
        foreach ($this->_compilers as $compiler) {
            $method = "_compile_{$compiler}";
            $template = $this->$method($template);
        }

        // Store compiled version in the cache
        $this->cache->file->save($cache_id, $template, $this->cache_time);

        // Return compiled template
        return $template;
    }


    /**
     * Runs a compiled template with its variables
     *
     * @param string $template
     * @param array $data
     * @return string
     */
    protected function _run($template, $data = NULL) {
        if (is_array($data)) {
            extract($data);
        }

        ob_start();
        eval(' ?>' . $template . '<?php ');
        $content = ob_get_clean();

        return $content;
    }


    /**
     * Get a template content for use inside the current template. Inherits
     * all global data, combined with local data passed as second argument.
     *
     * @param string $template
     * @param array $data
     * @return string
     */
    protected function _include($template, $data = NULL) {
        // Merge local data with global data
        $data = isset($data) ? array_merge($this->_data, $data) : $this->_data;

        // Compile and run template
        $compiled = $this->_compile($template);
        return $this->_run($compiled, $data);
    }


    /**
     * Gets a section content
     *
     * @param string $section
     * @return string
     */
    protected function _yield($section) {
        return isset($this->_sections[$section]) ? $this->_sections[$section] : '';
    }


    /**
     * Starts buffering section content
     *
     * @param string $section
     */
    protected function _section_start($section) {
        array_push($this->_last_section, $section);
        ob_start();
    }


    /**
     * Stops buffering section content. Returns the current section name.
     *
     * @return string
     */
    protected function _section_end() {
        $last = array_pop($this->_last_section);
        $this->_section_extend($last, ob_get_clean());

        return $last;
    }


    /**
     * Stores section content, replacing '@parent' with the previous section
     * content if any.
     *
     * @param string $section
     * @param string $content
     */
    protected function _section_extend($section, $content) {
        if (isset($this->_sections[$section])) {
            $this->_sections[$section] = str_replace('@parent', $content, $this->_sections[$section]);
        } else {
            $this->_sections[$section] = $content;
        }
    }

    /** 
     -------------------------------------------------------
    
                           COMPILERS
    
     -------------------------------------------------------
     */
    public function matcher($function) {
        return '/(\s*)@' . $function . '(\s*\(.*\))/';
    }


    /**
     * Rewrites Blade comments into PHP comments.
     *
     * @param  string  $value
     * @return string
     */
    protected function _compile_comments($value) {
        $value = preg_replace('/\{\{--(.+?)(--\}\})?\n/', "<?php // $1 ?>", $value);

return preg_replace('/\{\{--((.|\s)*?)--\}\}/', "<?php /* $1 */ ?>\n", $value);
}


/**
* Rewrites Blade echo statements into PHP echo statements.
*
* @param string $value
* @return string
*/
protected function _compile_echos($value) {
return preg_replace('/\{\{(.+?)\}\}/', '<?php echo $1; ?>', $value);
}


/**
* Rewrites Blade "for else" statements into valid PHP.
*
* @param string $value
* @return string
*/
protected function _compile_forelse($value) {
preg_match_all('/(\s*)@forelse(\s*\(.*\))(\s*)/', $value, $matches);

foreach ($matches[0] as $forelse) {
preg_match('/\$[^\s]*/', $forelse, $variable);

// Once we have extracted the variable being looped against, we can add
// an if statement to the start of the loop that checks if the count
// of the variable being looped against is greater than zero.
$if = "<?php if (count({$variable[0]}) > 0): ?>";

$search = '/(\s*)@forelse(\s*\(.*\))/';

$replace = '$1' . $if . '<?php foreach$2: ?>';

$blade = preg_replace($search, $replace, $forelse);

$value = str_replace($forelse, $blade, $value);
}

return $value;
}


/**
* Rewrites Blade "empty" statements into valid PHP.
*
* @param string $value
* @return string
*/
protected function _compile_empty($value) {
return str_replace('@empty', '<?php endforeach; ?><?php else: ?>', $value);
}


/**
* Rewrites Blade "forelse" endings into valid PHP.
*
* @param string $value
* @return string
*/
protected function _compile_endforelse($value) {
return str_replace('@endforelse', '<?php endif; ?>', $value);
}


/**
* Rewrites Blade structure openings into PHP structure openings.
*
* @param string $value
* @return string
*/
protected function _compile_structure_openings($value) {
$pattern = '/(\s*)@(if|elseif|foreach|for|while)(\s*\(.*\))/';

return preg_replace($pattern, '$1<?php $2$3: ?>', $value);
}


/**
* Rewrites Blade structure closings into PHP structure closings.
*
* @param string $value
* @return string
*/
protected function _compile_structure_closings($value) {
$pattern = '/(\s*)@(endif|endforeach|endfor|endwhile)(\s*)/';

return preg_replace($pattern, '$1<?php $2; ?>$3', $value);
}


/**
* Rewrites Blade else statements into PHP else statements.
*
* @param string $value
* @return string
*/
protected function _compile_else($value) {
return preg_replace('/(\s*)@(else)(\s*)/', '$1<?php $2: ?>$3', $value);
}


/**
* Rewrites Blade "unless" statements into valid PHP.
*
* @param string $value
* @return string
*/
protected function _compile_unless($value) {
$pattern = '/(\s*)@unless(\s*\(.*\))/';

return preg_replace($pattern, '$1<?php if( ! ($2)): ?>', $value);
}


/**
* Rewrites Blade "unless" endings into valid PHP.
*
* @param string $value
* @return string
*/
protected function _compile_endunless($value) {
return str_replace('@endunless', '<?php endif; ?>', $value);
}


/**
* Execute user defined compilers.
*
* @param string $value
* @return string
*/
protected function _compile_extensions($value) {
foreach ($this->_extensions as $compiler) {
$value = call_user_func($compiler, $value);
}

return $value;
}


/**
* Rewrites Blade @include statements into valid PHP.
*
* @param string $value
* @return string
*/
protected function _compile_includes($value) {
$pattern = static::matcher('include');

return preg_replace($pattern, '$1<?php echo $this->_include$2; ?>', $value);
}


/**
* Rewrites Blade "@layout" expressions into valid PHP.
*
* @param string $value
* @return string
*/
protected function _compile_layouts($value) {
$pattern = $this->matcher('layout');

// Find "@layout" expressions
if (!preg_match_all($pattern, $value, $matches, PREG_SET_ORDER)) {
return $value;
}

// Delete "@layout" expressions
$value = preg_replace($pattern, '', $value);

// Include layouts at the end of template
foreach ($matches as $set) {
$value .= "\n" . $set[1] . '<?php echo $this->_include' . $set[2] . "; ?>\n";
}

return $value;
}


/**
* Rewrites Blade @yield statements into Section statements.
*
* The Blade @yield statement is a shortcut to the Section::yield method.
*
* @param string $value
* @return string
*/
protected function _compile_yields($value) {
$pattern = $this->matcher('yield');

return preg_replace($pattern, '$1<?php echo $this->_yield$2; ?>', $value);
}


/**
* Rewrites Blade @section statements into Section statements.
*
* @param string $value
* @return string
*/
protected function _compile_section_start($value) {
$pattern = $this->matcher('section');

return preg_replace($pattern, '$1<?php $this->_section_start$2; ?>', $value);
}


/**
* Rewrites Blade @endsection statements into Section statements.
*
* @param string $value
* @return string
*/
protected function _compile_section_end($value) {
$replace = '<?php $this->_section_end(); ?>';

return str_replace('@endsection', $replace, $value);
}


/**
* Rewrites Blade yield section statements into valid PHP.
*
* @return string
*/
protected function _compile_yield_sections($value) {
$replace = '<?php echo $this->_yield($this->_section_end()); ?>';

return str_replace('@yield_section', $replace, $value);
}
}
