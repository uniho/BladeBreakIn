<?php

final class Compilers
{
  //
  public static function scss($name = false, $data = [], $options = [])
  {
    $core = new class {
      public function file($file, $data = [], $options = [])
      {
        $compiler = new class($data, $options) extends \Illuminate\View\Compilers\Compiler implements \Illuminate\View\Compilers\CompilerInterface
        {
          private $data;
          public function __construct($data, $options)
          {
            parent::__construct(app()['files'], app()['config']['view.compiled'], shouldCache: !isset($options['force_compile']));
            $this->data = $data;
          }
          public function compile($path)
          {
            $compiler = new \ScssPhp\ScssPhp\Compiler();

            $compiler->addImportPath(function($path) {
              if (\ScssPhp\ScssPhp\Compiler::isCssImport($path)) {
                return null;
              }

              $path = \HQ::getenv('CCC::SCSS_PATH').'/'.strtr($path, '.', '/').'.scss';
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
        return $engine->get($file);
      }

      public function exists($name)
      {
        return is_file($this->getFullName($name));
      }

      public function getFullName($name)
      {
        return \HQ::getenv('CCC::SCSS_PATH').'/'.strtr($name, '.', '/').'.scss';
      }
    };

    if (!$name) {
      return $core;
    }  

    return $core->file($core->getFullName($name), $data, $options);
  }

  //
  public static function markdown($name = false, $data = [], $options = [])
  {
    $core = new class {
      public function file($file, $data = [], $options = [])
      {
        $compiler = new class(
          app()['files'],
          app()['config']['view.compiled'],
          shouldCache: !isset($options['force_compile']),
        ) extends \Illuminate\View\Compilers\Compiler implements \Illuminate\View\Compilers\CompilerInterface
        {
          public function compile($path)
          {
            $config = [];
            // $env = new \League\CommonMark\Environment\Environment($config);
            // $env->addExtension(new \League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension());
            // $env->addExtension(new \League\CommonMark\Extension\GithubFlavoredMarkdownExtension());
            // $env->addExtension(new \League\CommonMark\Extension\FrontMatter\FrontMatterExtension());
            // $converter = new \League\CommonMark\MarkdownConverter($env);
            $converter = new \League\CommonMark\GithubFlavoredMarkdownConverter($config);
            $env = $converter->getEnvironment();
            $env->addExtension(new \League\CommonMark\Extension\FrontMatter\FrontMatterExtension());
        
            $env->addRenderer(
              \League\CommonMark\Extension\CommonMark\Node\Block\FencedCode::class,
              new class implements \League\CommonMark\Renderer\NodeRendererInterface {
                public function render($node, $childRenderer) {
                  $render = new \League\CommonMark\Extension\CommonMark\Renderer\Block\FencedCodeRenderer();
                  $htmlElement = $render->render($node, $childRenderer);
                  $contents = \str_replace('{', '&#123;', $htmlElement->getContents(false));
                  $htmlElement->setContents($contents);
                  return $htmlElement;
                }
              }
            );
        
            $env->addRenderer(
              \League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode::class,
              new class implements \League\CommonMark\Renderer\NodeRendererInterface {
                public function render($node, $childRenderer) {
                  $render = new \League\CommonMark\Extension\CommonMark\Renderer\Block\IndentedCodeRenderer();
                  $htmlElement = $render->render($node, $childRenderer);
                  $contents = \str_replace('{', '&#123;', $htmlElement->getContents(false));
                  $htmlElement->setContents($contents);
                  return $htmlElement;
                }
              }
            );
        
            $env->addRenderer(
              \League\CommonMark\Extension\CommonMark\Node\Inline\Code::class,
              new class implements \League\CommonMark\Renderer\NodeRendererInterface {
                public function render($node, $childRenderer) {
                  $render = new \League\CommonMark\Extension\CommonMark\Renderer\Inline\CodeRenderer();
                  $htmlElement = $render->render($node, $childRenderer);
                  $contents = \str_replace('{', '&#123;', $htmlElement->getContents(false));
                  $htmlElement->setContents($contents);
                  return $htmlElement;
                }
              }
            );
        
            $result = $converter->convert(\File::get($path));
            $frontMatter = [];
            if ($result instanceof \League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter) {
              $frontMatter = $result->getFrontMatter();
            }
            $contents = "---\n" . json_encode($frontMatter) . "\n---\n" . (string)$result;
        
            $this->ensureCompiledDirectoryExists(
              $compiledPath = $this->getCompiledPath($path)
            );

            $this->files->put($compiledPath, $contents);
          }
        };

        $engine = new \Illuminate\View\Engines\CompilerEngine($compiler, app()['files']);
        $contents = $engine->get($file);
        $frontmatter = '';
        $body = $contents;
        if (preg_match('{^\s*?---\s+([\S\s]*?)\s+---\s*([\S\s]*)$}', $contents, $match)) {
          $frontmatter = $match[1];
          $body = '';
          if (count($match) > 2) {
            $body = $match[2];
          }
        } 

        $data = array_merge(json_decode($frontmatter, true), $data);
        $m = new \Mustache();
        return $m->render($body, $data, $options);
      }

      public function exists($name)
      {
        return is_file($this->getFullName($name));
      }

      public function getFullName($name)
      {
        return \HQ::getenv('CCC::MARKDOWNS_PATH').'/'.strtr($name, '.', '/').'.md';
      }
    };

    if (!$name) {
      return $core;
    }  

    return $core->file($core->getFullName($name), $data);
  }
}
