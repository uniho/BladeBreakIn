<?php

namespace Utils;

final class Compilers
{
  public static function scssCompile($file, $data = [])
  {
    $compiler = new class($data) extends \Illuminate\View\Compilers\Compiler implements \Illuminate\View\Compilers\CompilerInterface
    {
      private $data;
      public function __construct($data)
      {
        parent::__construct(app()['files'], app()['config']['view.compiled']);
        $this->data = $data;
      }
      public function compile($path)
      {
        $compiler = new \ScssPhp\ScssPhp\Compiler();

        $compiler->addImportPath(function($path) {
          if (\ScssPhp\ScssPhp\Compiler::isCssImport($path)) {
            return null;
          }

          $path = './bd/scss/'.strtr($path, '.', '/').'.scss';
          if (!file_exists($path)) {
            return null;
          }

          return $path;
        });

        $compiler->addVariables($this->data);

        $contents = $compiler->compileString($this->files->get($path))->getCss();

        $this->ensureCompiledDirectoryExists(
          $compiledPath = $this->getCompiledPath($path)
        );

        $this->files->put($compiledPath, $contents);
      }
    };

    $engine = new \Illuminate\View\Engines\CompilerEngine($compiler, app()['files']);
    $file = './bd/scss/'.strtr($file, '.', '/').'.scss';
    return $engine->get($file);
  }
}
