<?php

namespace App\Presentation\Controllers;

use App\Core\Template\TemplateEngine;

/**
 * Base Controller with common functionality
 */
abstract class BaseController
{
    protected TemplateEngine $view;
    protected array $data = [];

    public function __construct()
    {
        $config = require __DIR__ . '/../../../config/app.php';
        $this->view = new TemplateEngine(
            $config['paths']['views'],
            $config['paths']['cache']
        );
        
        // Share global data with all views
        $this->shareGlobalData();
    }

    protected function render(string $template, array $data = []): void
    {
        echo $this->view->render($template, array_merge($this->data, $data));
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function session(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION;
    }

    protected function isAuthenticated(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }

    protected function getCurrentUser(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user'] ?? null;
    }

    protected function requireAuth(): void
    {
        if (!$this->isAuthenticated()) {
            $this->redirect('/login');
        }
    }

    protected function requireAdmin(): void
    {
        $this->requireAuth();
        $user = $this->getCurrentUser();
        if (!$user || $user['role'] !== 'admin') {
            $this->redirect('/');
        }
    }

    private function shareGlobalData(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->view->share('isAuthenticated', $this->isAuthenticated());
        $this->view->share('currentUser', $this->getCurrentUser());
        $this->view->share('appName', 'Hotel Booking System');
    }

    protected function validate(array $data, array $rules): array
    {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $rulesArray = explode('|', $fieldRules);
            
            foreach ($rulesArray as $rule) {
                if ($rule === 'required' && empty($value)) {
                    $errors[$field] = ucfirst($field) . ' is required';
                    break;
                }
                
                if (strpos($rule, 'min:') === 0 && strlen($value) < (int)substr($rule, 4)) {
                    $errors[$field] = ucfirst($field) . ' must be at least ' . substr($rule, 4) . ' characters';
                    break;
                }
                
                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = ucfirst($field) . ' must be a valid email';
                    break;
                }
            }
        }
        
        return $errors;
    }
}

