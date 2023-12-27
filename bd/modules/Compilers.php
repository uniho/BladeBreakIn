<?php

//
final class Compilers
{
  //
  public static function scss($name = false, $data = [], $options = [])
  {
    $core = new class extends _CompilerCore {

      //
      public function file($file, $data = [], $options = [])
      {
        $compiler = new class($this, $data, $options) extends _Compiler implements \Illuminate\View\Compilers\CompilerInterface
        {
          public function compile($path)
          {
            $contents = $this->core->inline($this->files->get($path), $this->data, $this->options);

            $this->ensureCompiledDirectoryExists(
              $compiledPath = $this->getCompiledPath($path)
            );

            $this->files->put($compiledPath, $contents);
          }
        };

        $engine = new \Illuminate\View\Engines\CompilerEngine($compiler, app()['files']);
        return $engine->get($file);
      }

      //
      public function inline($src, $data = [], $options = [])
      {
        $compiler = new \ScssPhp\ScssPhp\Compiler();

        $compiler->addImportPath(function($path) {
          if (\ScssPhp\ScssPhp\Compiler::isCssImport($path)) {
            return null;
          }

          $path = $this->getFullName($path);
          if (!file_exists($path)) {
            return null;
          }

          return $path;
        });

        $compiler->addVariables($data);

        return $compiler->compileString($src)->getCss();
      }

      //
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
    $core = new class extends _CompilerCore {

      //
      public function file($file, $data = [], $options = [])
      {
        $compiler = new class($this, $data, $options) extends _Compiler implements \Illuminate\View\Compilers\CompilerInterface
        {
          public function compile($path)
          {
            $contents = $this->core->inlineCommonMark($this->files->get($path));
        
            $this->ensureCompiledDirectoryExists(
              $compiledPath = $this->getCompiledPath($path)
            );

            $this->files->put($compiledPath, $contents);
          }
        };

        $engine = new \Illuminate\View\Engines\CompilerEngine($compiler, app()['files']);
        return $this->inlineMustache($engine->get($file), $data, $options);
      }

      //
      public function inline($src, $data = [], $options = [])
      {
        $src = $this->inlineCommonMark($src);
        return $this->inlineMustache($src, $data, $options);
      }

      //
      public function inlineCommonMark($src)
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
    
        $result = $converter->convert($src);
        $frontMatter = [];
        if ($result instanceof \League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter) {
          $frontMatter = $result->getFrontMatter();
        }
        
        return "---\n" . json_encode($frontMatter) . "\n---\n" . (string)$result;
      }

      //
      public function inlineMustache($src, $data = [], $options = [])
      {
        $frontmatter = '';
        $body = $src;
        if (preg_match('/\\A(?:---(.*?)?\\R---)(\\R.*)?\\Z/s', $src, $match)) {
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

      //
      public function getFullName($name)
      {
        return \HQ::getenv('CCC::MARKDOWNS_PATH').'/'.strtr($name, '.', '/').'.md';
      }
    };

    if (!$name) {
      return $core;
    }  

    return $core->file($core->getFullName($name), $data, $options);
  }

  //
  public static function js($name = false, $data = [], $options = [])
  {
    $core = new class extends _CompilerCore {
      
      //
      public function file($file, $data = [], $options = [])
      {
        $compiler = new class($this, $data, $options) extends _Compiler implements \Illuminate\View\Compilers\CompilerInterface
        {
          public function compile($path)
          {
            $contents = $this->core->inline($this->files->get($path));

            $this->ensureCompiledDirectoryExists(
              $compiledPath = $this->getCompiledPath($path)
            );

            $this->files->put($compiledPath, $contents);
          }
          
        };

        $engine = new \Illuminate\View\Engines\CompilerEngine($compiler, app()['files']);
        return $engine->get($file);
      }

      //
      public function inline($src, $data = [], $options = [])
      {
        $minify = new JavaScriptMinify();
        return $minify->render($src, $options);
      }

      //
      public function getFullName($name)
      {
        return \HQ::getenv('CCC::JS_PATH').'/'.strtr($name, '.', '/').'.js';
      }
    };

    if (!$name) {
      return $core;
    }  

    return $core->file($core->getFullName($name), $data, $options);
  }
}

//
abstract class _CompilerCore
{
  abstract public function file($file, $data = [], $options = []);

  public function exists($name)
  {
    return is_file($this->getFullName($name));
  }

  abstract public function getFullName($name);
}

//
class _Compiler extends \Illuminate\View\Compilers\Compiler
{
  public function __construct(protected $core, protected $data, protected $options)
  {
    parent::__construct(app()['files'], app()['config']['view.compiled'], shouldCache: !isset($options['force_compile']) || !$options['force_compile']);
  }
}

//
final class JavaScriptMinify
{
  const TAG_JSX = [ // JSX として取り扱うタグ関数の名前 
    'html',
  ]; 

  const TAG_CSS = [ // CSS として取り扱うタグ関数の名前
    'css',
    'keyframes',
    'styled()',
  ];

  const SEPARATOR = "\x89sep\x89";
  const CR = "\x89cr\x89";

  public function render($src, $options = [])
  {
    $peastOptions = [
      'sourceType' => \Peast\Peast::SOURCE_TYPE_MODULE,
      // 'jsx' => true,
    ];

    // AST generation
    $ast = \Peast\Peast::latest($src, $peastOptions)->parse();

    // Tree Traversing
    $traverser = new \Peast\Traverser;
    $traverser->addFunction(function($node) {
      $type = $node->getType();
      
      // タグ関数を探す
      if ($type === 'TaggedTemplateExpression') {
        
        $tagName = false;
        $t = $node->gettag();
        if (method_exists($t, 'getname')) {
          $tagName = $t->getname();
        } else {
          if (!method_exists($t, 'getCallee')) return;
          $calee = $t->getCallee();
          if (method_exists($calee, 'getName')) {
            $tagName = $calee->getName() . '()';
          } else {
            if (!method_exists($calee, 'getProperty')) return;
            $property = $calee->getProperty();
            if (!method_exists($property, 'getName')) return;
            $tagName = $property->getName() . '()';
          }
        }

        // JSX の圧縮
        if (in_array($tagName, self::TAG_JSX)) {
          // テンプレートリテラルを取得
          // ${} 部分で区切られた配列となっている
          $src = '';
          $sls = $node->getquasi()->getquasis(); // 文字列部分
          foreach ($sls as $key => $sl) {
            $src .= ($key == 0 ? '' : self::SEPARATOR) . $sl->getrawValue();
          }
          $src = $this->minimize_jsx($src);
          $arr = explode(self::SEPARATOR, $src);
          foreach ($sls as $key => $sl) {
            $sl->setrawValue($arr[$key]);
          }
        }

        // CSS の圧縮
        if (in_array($tagName, self::TAG_CSS)) {
          // テンプレートリテラルを取得
          // ${} 部分で区切られた配列となっている
          $sls = $node->getquasi()->getquasis(); // 文字列部分
          foreach ($sls as $sl) {
            $src = $sl->getrawValue();
            $src = $this->minimize_css($src);
            $sl->setrawValue($src);
          }
        }
      } 
    });
    $traverser->traverse($ast);
    
    // Render
    $renderer = new \Peast\Renderer;
    $renderer->setFormatter(
      isset($options['pretty_print']) ? new \Peast\Formatter\PrettyPrint : new \Peast\Formatter\Compact
    );

    return $renderer->render($ast);
  }
  
  private function minimize_jsx($buffer)
  {
    $replaces = [];

    // <style>
    if (preg_match_all('[<style(?: [^>]+)?>(.+?)</style>]is', $buffer, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $match) {
        $replaces[$match[1]] = minimize_css($match[1]);
      }
    }

    // Hold "\n" when tag is <pre> or <textarea>.
    if (preg_match_all('[<(?:pre|textarea)(?: [^>]+)?>(.+?)</(?:pre|textarea)>]is', $buffer, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $match) {
        $replaces[$match[1]] = str_replace("\n", self::CR, $match[1]);
      }
    }

    if (!empty($replaces)) {
      $buffer = str_replace(array_keys($replaces), array_values($replaces), $buffer);
    }

    $replaces = [];

    // Remove comments
    $replaces['[<!--(?![<>\[\]]).*?(?<![<>\[\]])-->]s'] = '';

    // Remove spaces after newline characters.
    $replaces['[\n\s*(\S)]s'] = ' ${1}';

    $buffer = preg_replace(array_keys($replaces), array_values($replaces), $buffer);

    // Remove leading and trailing whitespace, and recover \n in <pre> or <textarea>.
    $buffer = str_replace(self::CR, "\n", trim($buffer));

    return $buffer;
  }

  private function minimize_css($buffer)
  {
    // ※ reduced the processing intensity.
    $search = array(
      // remove comments
      '/(\/\*!.*?\*\/|\"(?:(?!(?<!\\\)\").)*\"|\'(?:(?!(?<!\\\)\').)*\')|\/\*.*?\*\/|\/\/[^\r\n]+[\r\n]/s',
      // shorten multiple whitespace sequences
      '/\s+/s',
    );
    $replace = array(
      '${1}',
      ' ',
    );
    return preg_replace($search, $replace, $buffer);
  }
}