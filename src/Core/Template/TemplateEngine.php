<?php

namespace App\Core\Template;

/**
 * Custom Template Engine
 * Supports: layouts, components, partials, sections, and expressions
 */
class TemplateEngine
{
    private string $viewPath;
    private string $cachePath;
    private array $sections = [];
    private array $globals = [];
    private ?string $currentSection = null;
    private ?string $layout = null;
    private bool $cacheEnabled = true;

    public function __construct(string $viewPath, string $cachePath, bool $cacheEnabled = true)
    {
        $this->viewPath = rtrim($viewPath, '/');
        $this->cachePath = rtrim($cachePath, '/');
        $this->cacheEnabled = $cacheEnabled;

        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0755, true);
        }
    }

    /**
     * Render a view template
     */
    public function render(string $view, array $data = []): string
    {
        // Preserve existing sections if rendering a layout
        $preserveSections = !empty($this->sections);
        $savedSections = $this->sections;

        if (!$preserveSections) {
            $this->layout = null;
            $this->sections = [];
            $this->currentSection = null;
        }

        $data = array_merge($this->globals, $data);

        $compiled = $this->compile($view);

        ob_start();
        extract($data);
        eval('?>' . $compiled);
        $content = ob_get_clean();

        if ($this->layout) {
            // Only set content if no content section was explicitly defined
            if (!isset($this->sections['content'])) {
                $this->sections['content'] = $content;
            }
            return $this->renderLayout($this->layout, $data);
        }

        // If we preserved sections, restore and merge them
        if ($preserveSections) {
            $this->sections = array_merge($savedSections, $this->sections);
        }

        return $content;
    }

    /**
     * Compile template to PHP code
     */
    private function compile(string $view): string
    {
        $viewFile = $this->viewPath . '/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            throw new \Exception("View not found: {$view}");
        }

        $cacheFile = $this->cachePath . '/' . md5($view) . '.php';

        if ($this->cacheEnabled && file_exists($cacheFile) &&
            filemtime($cacheFile) >= filemtime($viewFile)) {
            return file_get_contents($cacheFile);
        }

        $content = file_get_contents($viewFile);
        $compiled = $this->compileTemplate($content);

        if ($this->cacheEnabled) {
            file_put_contents($cacheFile, $compiled);
        }

        return $compiled;
    }

    /**
     * Compile template directives
     */
    private function compileTemplate(string $content): string
    {
        // @extends directive (must be first)
        $content = preg_replace('/@extends\([\'"](.+?)[\'"]\)/', '<?php $this->extend("$1"); ?>', $content);

        // @section and @endsection
        $content = preg_replace('/@section\([\'"](.+?)[\'"]\)/', '<?php $this->startSection("$1"); ?>', $content);
        $content = preg_replace('/@endsection/', '<?php $this->endSection(); ?>', $content);

        // @yield directive
        $content = preg_replace('/@yield\([\'"](.+?)[\'"]\)/', '<?php echo $this->yieldSection("$1"); ?>', $content);

        // Control structures - use helper to handle nested parentheses
        $content = $this->compileControlStructure($content, 'if', 'if($1): ?>');
        $content = $this->compileControlStructure($content, 'elseif', 'elseif($1): ?>');
        $content = preg_replace('/@else\b/', '<?php else: ?>', $content);
        $content = preg_replace('/@endif\b/', '<?php endif; ?>', $content);

        // @foreach, @endforeach
        $content = $this->compileControlStructure($content, 'foreach', 'foreach($1): ?>');
        $content = preg_replace('/@endforeach\b/', '<?php endforeach; ?>', $content);

        // @for, @endfor
        $content = $this->compileControlStructure($content, 'for', 'for($1): ?>');
        $content = preg_replace('/@endfor\b/', '<?php endfor; ?>', $content);

        // @while, @endwhile
        $content = $this->compileControlStructure($content, 'while', 'while($1): ?>');
        $content = preg_replace('/@endwhile\b/', '<?php endwhile; ?>', $content);

        // @include directive
        $content = preg_replace_callback('/@include\([\'"](.+?)[\'"](?:,\s*(\[.+?\]))?\)/', function($matches) {
            $view = $matches[1];
            $data = $matches[2] ?? '[]';
            return "<?php echo \$this->includePartial('{$view}', {$data}); ?>";
        }, $content);

        // @component directive
        $content = preg_replace_callback('/@component\([\'"](.+?)[\'"](?:,\s*(\[.+?\]))?\)/', function($matches) {
            $view = $matches[1];
            $data = $matches[2] ?? '[]';
            return "<?php echo \$this->includePartial('{$view}', {$data}); ?>";
        }, $content);

        // @csrf
        $content = preg_replace('/@csrf\b/', '<?php echo $this->csrf(); ?>', $content);

        // @method
        $content = preg_replace('/@method\([\'"](.+?)[\'"]\)/', '<?php echo $this->method("$1"); ?>', $content);

        // {{ $variable }} - escaped output (do this AFTER control structures)
        $content = preg_replace('/\{\{\s*(.+?)\s*\}\}/', '<?php echo htmlspecialchars($1, ENT_QUOTES, "UTF-8"); ?>', $content);

        // {!! $variable !!} - raw output
        $content = preg_replace('/\{!!\s*(.+?)\s*!!\}/', '<?php echo $1; ?>', $content);

        return $content;
    }

    /**
     * Compile control structures with balanced parentheses
     */
    private function compileControlStructure(string $content, string $directive, string $replacement): string
    {
        $pattern = '/@' . $directive . '\s*\(/';
        $offset = 0;

        while (preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE, $offset)) {
            $start = $matches[0][1];
            $parenStart = $start + strlen($matches[0][0]) - 1; // Position of opening (

            // Find matching closing parenthesis
            $parenCount = 1;
            $i = $parenStart + 1;
            $len = strlen($content);

            while ($i < $len && $parenCount > 0) {
                if ($content[$i] === '(') {
                    $parenCount++;
                } elseif ($content[$i] === ')') {
                    $parenCount--;
                }
                $i++;
            }

            if ($parenCount === 0) {
                // Extract the expression between parentheses
                $expression = substr($content, $parenStart + 1, $i - $parenStart - 2);
                $fullMatch = substr($content, $start, $i - $start);
                $compiled = '<?php ' . str_replace('$1', $expression, $replacement);

                $content = substr_replace($content, $compiled, $start, strlen($fullMatch));
                $offset = $start + strlen($compiled);
            } else {
                $offset = $i;
            }
        }

        return $content;
    }

    /**
     * Extend a layout
     */
    public function extend(string $layout): void
    {
        $this->layout = $layout;
    }

    /**
     * Start a section
     */
    public function startSection(string $name): void
    {
        $this->currentSection = $name;
        ob_start();
    }

    /**
     * End current section
     */
    public function endSection(): void
    {
        if ($this->currentSection) {
            $this->sections[$this->currentSection] = ob_get_clean();
            $this->currentSection = null;
        }
    }

    /**
     * Yield a section
     */
    public function yieldSection(string $name, string $default = ''): string
    {
        return $this->sections[$name] ?? $default;
    }

    /**
     * Include a partial/component
     */
    public function includePartial(string $view, array $data = []): string
    {
        $engine = new self($this->viewPath, $this->cachePath, $this->cacheEnabled);
        $engine->globals = array_merge($this->globals, $data);
        return $engine->render($view, $data);
    }

    /**
     * Render layout
     */
    private function renderLayout(string $layout, array $data): string
    {
        $engine = new self($this->viewPath, $this->cachePath, $this->cacheEnabled);
        $engine->sections = $this->sections;
        $engine->globals = $this->globals;
        return $engine->render($layout, $data);
    }

    /**
     * Add global variables
     */
    public function share(string $key, $value): void
    {
        $this->globals[$key] = $value;
    }

    /**
     * CSRF token field
     */
    public function csrf(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
    }

    /**
     * HTTP method field
     */
    public function method(string $method): string
    {
        return '<input type="hidden" name="_method" value="' . strtoupper($method) . '">';
    }
}
