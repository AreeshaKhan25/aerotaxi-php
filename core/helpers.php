<?php
/**
 * AeroTAXI - Helper Functions
 * Replaces Laravel's helper functions, CSRF, session flash, etc.
 */

// ── Session helpers ──────────────────────────────────────────────────

function ensure_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// ── CSRF Protection ──────────────────────────────────────────────────

function csrf_token(): string
{
    ensure_session();
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf(): bool
{
    ensure_session();
    $token = $_POST['_csrf_token'] ?? '';
    return hash_equals($_SESSION['_csrf_token'] ?? '', $token);
}

// ── Flash messages ───────────────────────────────────────────────────

function flash(string $key, $value): void
{
    ensure_session();
    $_SESSION['_flash'][$key] = $value;
}

function get_flash(string $key, $default = null)
{
    ensure_session();
    $value = $_SESSION['_flash'][$key] ?? $default;
    unset($_SESSION['_flash'][$key]);
    return $value;
}

function has_flash(string $key): bool
{
    ensure_session();
    return isset($_SESSION['_flash'][$key]);
}

// ── Old input (re-populate forms after validation failure) ───────────

function flash_old(array $data): void
{
    ensure_session();
    $_SESSION['_old_input'] = $data;
}

function old(string $key, $default = '')
{
    ensure_session();
    return $_SESSION['_old_input'][$key] ?? $default;
}

function clear_old(): void
{
    unset($_SESSION['_old_input']);
}

function set_old_values(): void
{
    ensure_session();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        flash_old($_POST);
    }
}

function clear_old_values(): void
{
    clear_old();
}

// ── Validation errors ────────────────────────────────────────────────

function flash_errors(array $errors): void
{
    ensure_session();
    $_SESSION['_flash']['errors'] = $errors;
}

function get_errors(): array
{
    ensure_session();
    $errors = $_SESSION['_flash']['errors'] ?? [];
    unset($_SESSION['_flash']['errors']);
    return $errors;
}

function has_errors(): bool
{
    ensure_session();
    return !empty($_SESSION['_flash']['errors']);
}

function flash_error(string $message): void
{
    ensure_session();
    if (!isset($_SESSION['_flash']['errors'])) {
        $_SESSION['_flash']['errors'] = [];
    }
    $_SESSION['_flash']['errors'][] = $message;
}

function flash_success(string $message): void
{
    flash('success', $message);
}

function get_success(): ?string
{
    return get_flash('success');
}

// ── Redirect ─────────────────────────────────────────────────────────

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function redirect_back(): void
{
    $referer = $_SERVER['HTTP_REFERER'] ?? '/';
    redirect($referer);
}

// ── Output helpers ───────────────────────────────────────────────────

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function json_response(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// ── URL helpers ──────────────────────────────────────────────────────

function base_url(string $path = ''): string
{
    // Detect base URL from the script
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $base = rtrim($scriptDir, '/');
    return $base . '/' . ltrim($path, '/');
}

function current_url(): string
{
    return $_SERVER['REQUEST_URI'] ?? '/';
}

function is_current(string $path): bool
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
    $check = base_url($path);
    return $uri === $check;
}

function is_current_prefix(string $path): bool
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
    $check = base_url($path);
    return str_starts_with($uri, $check);
}

// ── Admin auth ───────────────────────────────────────────────────────

function admin_logged_in(): bool
{
    ensure_session();
    return !empty($_SESSION['admin_id']);
}

function admin_user(): ?object
{
    ensure_session();
    if (!admin_logged_in()) return null;
    return fetch("SELECT * FROM admins WHERE id = ?", [$_SESSION['admin_id']]);
}

function require_admin(): void
{
    if (!admin_logged_in()) {
        redirect(base_url('admin/login'));
    }
}

// ── Misc helpers ─────────────────────────────────────────────────────

function generate_reference(): string
{
    return 'ATH-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
}

function time_ago(string $datetime): string
{
    $now = new DateTime();
    $then = new DateTime($datetime);
    $diff = $now->diff($then);

    if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i > 0) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    return 'just now';
}

function format_date(?string $date, string $format = 'd M Y'): string
{
    if (!$date) return '—';
    return date($format, strtotime($date));
}

function str_limit(?string $str, int $limit = 60): string
{
    if (!$str) return '';
    if (mb_strlen($str) <= $limit) return $str;
    return mb_substr($str, 0, $limit) . '...';
}

// ── URL generation ──────────────────────────────────────────────────

function url(string $path = ''): string
{
    return base_url($path);
}

// ── Validation ───────────────────────────────────────────────────────

function validate(array $data, array $rules): array
{
    $errors = [];
    
    foreach ($rules as $field => $rule_string) {
        $rules_array = explode('|', $rule_string);
        $value = $data[$field] ?? null;
        
        foreach ($rules_array as $rule) {
            $parts = explode(':', $rule);
            $rule_name = $parts[0];
            $rule_param = $parts[1] ?? null;
            
            if ($rule_name === 'required' && empty($value)) {
                $errors[$field] = ucfirst($field) . ' is required.';
            } elseif ($rule_name === 'email' && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = ucfirst($field) . ' must be a valid email.';
            } elseif ($rule_name === 'min' && !empty($value) && strlen($value) < (int)$rule_param) {
                $errors[$field] = ucfirst($field) . ' must be at least ' . $rule_param . ' characters.';
            } elseif ($rule_name === 'max' && !empty($value) && strlen($value) > (int)$rule_param) {
                $errors[$field] = ucfirst($field) . ' must not exceed ' . $rule_param . ' characters.';
            } elseif ($rule_name === 'numeric' && !empty($value) && !is_numeric($value)) {
                $errors[$field] = ucfirst($field) . ' must be numeric.';
            }
        }
    }
    
    return $errors;
}

// ── Debug helpers ───────────────────────────────────────────────────

function dd($data): void
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    exit;
}

function log_error(string $message): void
{
    $log_file = BASE_PATH . '/storage/logs/error.log';
    $dir = dirname($log_file);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($log_file, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND);
}

// ── Auth helpers ────────────────────────────────────────────────────

function require_auth(): void
{
    require_admin();
}

/**
 * Lightweight Collection emulation for classical PHP
 */
class SimpleCollection implements IteratorAggregate, Countable, ArrayAccess, JsonSerializable
{
    protected array $items;

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function where(string $key, $operator, $value = null): self
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $filtered = array_filter($this->items, function ($item) use ($key, $operator, $value) {
            $actual = is_object($item) ? ($item->$key ?? null) : ($item[$key] ?? null);
            switch ($operator) {
                case '=':
                case '==':
                    return $actual == $value;
                case '===':
                    return $actual === $value;
                case '!=':
                case '<>':
                    return $actual != $value;
                case '!==':
                    return $actual !== $value;
                case '<':
                    return $actual < $value;
                case '>':
                    return $actual > $value;
                case '<=':
                    return $actual <= $value;
                case '>=':
                    return $actual >= $value;
                default:
                    return $actual == $value;
            }
        });
        return new self(array_values($filtered));
    }

    public function sum(string $key): float
    {
        $sum = 0;
        foreach ($this->items as $item) {
            if (is_object($item)) {
                $sum += (float) ($item->$key ?? 0);
            } elseif (is_array($item)) {
                $sum += (float) ($item[$key] ?? 0);
            }
        }
        return $sum;
    }

    public function pluck(string $valueKey, ?string $keyKey = null): array
    {
        $plucked = [];
        foreach ($this->items as $item) {
            if (is_object($item)) {
                $v = $item->$valueKey ?? null;
                if ($keyKey !== null) {
                    $k = $item->$keyKey ?? null;
                    $plucked[$k] = $v;
                } else {
                    $plucked[] = $v;
                }
            } elseif (is_array($item)) {
                $v = $item[$valueKey] ?? null;
                if ($keyKey !== null) {
                    $k = $item[$keyKey] ?? null;
                    $plucked[$k] = $v;
                } else {
                    $plucked[] = $v;
                }
            }
        }
        return $plucked;
    }

    public function map(callable $callback): self
    {
        return new self(array_map($callback, $this->items));
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function offsetExists($offset): bool { return isset($this->items[$offset]); }
    public function offsetGet($offset): mixed { return $this->items[$offset] ?? null; }
    public function offsetSet($offset, $value): void { $this->items[$offset] = $value; }
    public function offsetUnset($offset): void { unset($this->items[$offset]); }

    public function all(): array { return $this->items; }

    public function jsonSerialize(): mixed
    {
        return $this->items;
    }
}

function collect(array $items = []): SimpleCollection
{
    return new SimpleCollection($items);
}

/**
 * Send an email using SMTP (PHPMailer) or native mail as fallback
 */
function send_mail(string $to, string $subject, string $body, bool $isHtml = true): bool
{
    // Try using PHPMailer if SMTP is configured
    if (MAIL_MAILER === 'smtp' && !empty(MAIL_HOST) && !empty(MAIL_USERNAME)) {
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            
            // Encryption
            if (MAIL_ENCRYPTION === 'ssl') {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            }
            $mail->Port       = MAIL_PORT;
            
            // Timeout settings (reduce default to avoid hanging)
            $mail->Timeout = 10;
            
            // Recipients
            $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
            $mail->addAddress($to);
            
            // Content
            $mail->isHTML($isHtml);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            
            // Disable SSL certificate verification if running on local environment
            if (APP_ENV === 'development') {
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];
            }
            
            return $mail->send();
        } catch (\Exception $e) {
            log_error("SMTP Mail Send Failure: " . $e->getMessage());
            // Fall back to native php mail() if SMTP fails
        }
    }
    
    // Fallback: native PHP mail
    $headers = [
        'From' => MAIL_FROM_NAME . ' <' . MAIL_FROM_ADDRESS . '>',
        'Reply-To' => MAIL_FROM_ADDRESS,
        'X-Mailer' => 'PHP/' . phpversion()
    ];
    if ($isHtml) {
        $headers['MIME-Version'] = '1.0';
        $headers['Content-type'] = 'text/html; charset=utf-8';
    }
    
    return @mail($to, $subject, $body, $headers);
}

/**
 * Render an email template with data
 */
function render_email(string $template, array $data): string
{
    extract($data);
    ob_start();
    include BASE_PATH . '/templates/emails/' . $template . '.php';
    return ob_get_clean();
}
