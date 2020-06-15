<?php
namespace Zoom\View;

use Zoom\FS;

class View {

    
    /**
     * The relative path to the compiled view
     * 
     * @var string
     */
    protected $compiledFilePath;

    /**
     * Possible paths to expected file types
     * 
     * @var array
     */
    protected $data;

    /**
     * The function name of the compiled view
     * 
     * @var string
     */
    protected $functionName;

    /**
     * The dot syntax view name
     * 
     * @var string
     */
    protected $name;

    /**
     * The view's file path with no extension
     * 
     * @var string
     */
    protected $path;

    /**
     * Possible paths to expected file types
     * 
     * @var array
     */
    protected $paths = [];

    /**
     * Possible paths to expected file types
     * 
     * @var string
     */
    protected $sections;

    /**
     * Search and replace patterns
     * 
     * @var array
     */
    protected $searchNReplace = [
        '/\@\((.*?(?=\)\@))\)\@/'               => '<?= __($1); ?>',
        '/{{\-\-(.*?(?=\-\-}}))\-\-}}/'         => '<?php /*$1*/ ?>',
        '/{!!(.*?(?=!!}))!!}/'                  => '<?= $1; ?>',
        '/{{(.*?(?=}}))}}/'                     => '<?= e($1); ?>',
        '/\@asset\((.*?(?=\)\@))\)\@/'          => '<?= asset($1); ?>',
        '/\@url\((.*?(?=\)\@))\)\@/'            => '<?= url($1); ?>',
        '/\@csrf\((.*?(?=\)))\)/'               => '<?= csrf($1); ?>',
        '/\@elseif\s*(\(.*\))/'                 => '<?php } else if $1 { ?>',
        '/\@else/'                              => '<?php } else { ?>',
        '/\@endsection/'                        => '<?php $__view->stopSection(); ?>',
        '/\@end\w+/'                            => '<?php } ?>',
        '/\@extends\s*\((.*)\)((.*\R)*.*\Z)/'   => '$2<?= view( $1, $__view->getData(), $__view->getSections() ); ?>',
        '/\@for(\w*)\s*(\(.*\))/'               => '<?php for$1 $2 { ?>',
        '/\@if\s*(\(.*\))/'                     => '<?php if $1 { ?>',
        '/\@include\((.*)\)/'                   => '<?= view( $1, $__view->getData(), $__view->getSections() ); ?>',
        '/\@section\((.*)\)/'                  => '<?php $__view->startSection($1); ?>',
        '/\@yield\((.*?(?=\)))\)/'             => '<?= $__view->yieldContent($1); ?>',
    ];

    /**
     * Possible paths to expected file types
     * 
     * @var string
     */
    protected $tempSection;
    
    /**
     * Create a new View instance
     * 
     * @param   string  $name
     * @param   array   $data
     * @param   array   $extension_preferences
     * 
     * @return  void
     */
    public function __construct($name, array $data, array $sections = [], $extension_preferences = ['php', 'html', 'text']) {
        $this->data = $data;
        $this->name = $name;
        $this->sections = $sections;
        $this->compiledFilePath = 'framework/views/' . md5($name);
        $this->functionName = 'view_' . str_replace('.', '_', $name);
        $this->path = FS::disk('views')->path(str_replace('.', '/', $name));
        foreach ($extension_preferences as $extension) {
            $this->paths[$extension] = "{$this->path}.{$extension}";
        }
    }

    /**
     * Return a compiled view
     */
    public function compile() {
        $path = $this->getPreferedPath();
        ob_start();
        include $path;
        $func = "<?php function {$this->functionName}(\$__view";
        foreach($this->data as $var => $val) {
            $func .= ", \${$var}";
        }
        $func .= ") { ?>";
        $func_view = preg_replace(
            array_keys($this->searchNReplace),
            array_values($this->searchNReplace),
            $func . ob_get_clean()
        ) . "\n<?php } \n";
        FS::disk('storage')->save($this->compiledFilePath, $func_view);
        return $this;
    }

    /**
     * Get the data passed to the view
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Get the sections compiled by the view
     */
    public function getSections() {
        return $this->sections;
    }

    /**
     * Get the preferred extension path
     * 
     * @return  string
     */
    protected function getPreferedPath() {
        foreach ($this->paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        throw new ViewNotFoundException("The view '{$this->name}' could not be found.");
    }

    /**
     * Return a compiled view
     */
    public function render() {
        ob_start();
        include FS::path($this->compiledFilePath);
        call_user_func_array($this->functionName, array_merge([$this], array_values($this->data)));
        return trim(ob_get_clean());
    }

    /**
     * Return the section requested
     * 
     * @param   string  $key
     * @param   string  $value
     * @return  mixed
     */
    public function startSection($key, $value = null) {
        ob_start();
        $this->tempSection = $key;
        if (is_string($value)) {
            echo $value;
            $this->stopSection();
        }
    }

    /**
     * Return the section requested
     * 
     * @return  mixed
     */
    public function stopSection() {
        if (ob_get_level() > 2) {
            $this->sections[$this->tempSection] = ob_get_clean();
        }
    }

    /**
     * Return the section requested
     * 
     * @return  string
     */
    public function yieldContent($key) {
        return isset($this->sections[$key]) ? $this->sections[$key] : null;
    }

}
