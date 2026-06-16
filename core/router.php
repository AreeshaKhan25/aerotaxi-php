<?php
/**
 * AeroTAXI - Simple Router
 */

class Router
{
    protected $getRoutes = [];
    protected $postRoutes = [];

    public function get(string $path, string $file): self
    {
        $this->getRoutes[$path] = $file;
        return $this;
    }

    public function post(string $path, string $file): self
    {
        $this->postRoutes[$path] = $file;
        return $this;
    }

    public function dispatch(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        // Strip the base path
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $base = rtrim($scriptDir, '/');
        $path = $uri;
        if ($base && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }
        $path = '/' . trim($path, '/');

        // Handle GET routes
        if ($method === 'GET' && isset($this->getRoutes[$path])) {
            $this->executeRoute($this->getRoutes[$path], $path);
            return;
        }

        // Handle POST routes
        if ($method === 'POST' && isset($this->postRoutes[$path])) {
            $this->executeRoute($this->postRoutes[$path], $path);
            return;
        }

        // Handle dynamic routes with {id}
        foreach ($this->getRoutes as $pattern => $file) {
            if ($this->matchDynamicRoute($pattern, $path, $matches, $method, 'GET')) {
                $this->executeRoute($file, $path, $matches);
                return;
            }
        }

        foreach ($this->postRoutes as $pattern => $file) {
            if ($this->matchDynamicRoute($pattern, $path, $matches, $method, 'POST')) {
                $this->executeRoute($file, $path, $matches);
                return;
            }
        }

        // 404
        http_response_code(404);
        echo '<!DOCTYPE html><html><head><title>404 - Not Found</title>';
        echo '<script src="https://cdn.tailwindcss.com"></script></head>';
        echo '<body class="bg-gray-50 min-h-screen flex items-center justify-center">';
        echo '<div class="text-center"><h1 class="text-6xl font-bold text-gray-300 mb-4">404</h1>';
        echo '<p class="text-gray-500 mb-6">Page not found</p>';
        echo '<a href="' . base_url('/') . '" class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold rounded-xl px-6 py-3 transition">Go Home</a>';
        echo '</div></body></html>';
        exit;
    }

    protected function matchDynamicRoute(string $pattern, string $path, &$matches, string $method, string $expected_method): bool
    {
        if ($method !== $expected_method) {
            return false;
        }

        // Convert {id} to regex pattern
        $regex = preg_replace('/\{([a-z_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';
        
        if (preg_match($regex, $path, $matches)) {
            return true;
        }
        return false;
    }

    protected function executeRoute(string $file, string $path, array $matches = []): void
    {
        // Check admin routes
        if (str_starts_with($path, '/admin') && $path !== '/admin/login') {
            require_admin();
        }

        // Export matches to local variables
        extract($matches);

        require BASE_PATH . '/' . $file;
        exit;
    }
}

/**
 * Get a router instance
 */
function router(): Router
{
    return new Router();
}
