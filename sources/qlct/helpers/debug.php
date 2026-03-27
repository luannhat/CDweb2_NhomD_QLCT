<?php
/**
 * Debug Helper Functions
 * Sử dụng: require_once __DIR__ . '/helpers/debug.php';
 */

/**
 * Dump and Die - In dữ liệu và dừng thực thi
 */
function dd($data, $title = 'Debug Data') {
    echo '<div style="background:#1e1e1e;color:#fff;padding:20px;margin:10px;border-radius:8px;font-family:monospace;">';
    echo '<h3 style="color:#4ec9b0;margin:0 0 10px 0;">🐛 ' . htmlspecialchars($title) . '</h3>';
    echo '<pre style="margin:0;color:#ce9178;">';
    print_r($data);
    echo '</pre>';
    echo '</div>';
    die();
}

/**
 * Dump - In dữ liệu nhưng tiếp tục thực thi
 */
function dump($data, $title = 'Debug Data') {
    echo '<div style="background:#1e1e1e;color:#fff;padding:20px;margin:10px;border-radius:8px;font-family:monospace;">';
    echo '<h3 style="color:#4ec9b0;margin:0 0 10px 0;">🐛 ' . htmlspecialchars($title) . '</h3>';
    echo '<pre style="margin:0;color:#ce9178;">';
    print_r($data);
    echo '</pre>';
    echo '</div>';
}

/**
 * Debug Log - Ghi vào error log
 */
function debug_log($message, $data = null) {
    $timestamp = date('Y-m-d H:i:s');
    $log = "[$timestamp] DEBUG: $message";
    
    if ($data !== null) {
        $log .= "\n" . print_r($data, true);
    }
    
    error_log($log);
}

/**
 * SQL Debug - Log SQL query
 */
function debug_sql($sql, $params = []) {
    $timestamp = date('Y-m-d H:i:s');
    $log = "[$timestamp] SQL: $sql";
    
    if (!empty($params)) {
        $log .= "\nParams: " . print_r($params, true);
    }
    
    error_log($log);
}

/**
 * Debug to Console - In ra Chrome DevTools Console
 */
function console_log($data, $title = 'Debug') {
    $json = json_encode($data, JSON_UNESCAPED_UNICODE);
    echo "<script>console.log('🐛 $title:', $json);</script>";
}

/**
 * Performance Timer
 */
class DebugTimer {
    private static $timers = [];
    
    public static function start($name = 'default') {
        self::$timers[$name] = microtime(true);
    }
    
    public static function end($name = 'default', $log = true) {
        if (!isset(self::$timers[$name])) {
            return null;
        }
        
        $elapsed = microtime(true) - self::$timers[$name];
        $time = round($elapsed * 1000, 2); // Convert to ms
        
        if ($log) {
            error_log("⏱️  Timer '$name': {$time}ms");
        }
        
        unset(self::$timers[$name]);
        return $elapsed;
    }
}

/**
 * Trace function calls
 */
function debug_trace() {
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
    $output = "Function call trace:\n";
    
    foreach ($trace as $i => $t) {
        $file = isset($t['file']) ? basename($t['file']) : 'unknown';
        $line = isset($t['line']) ? $t['line'] : '?';
        $function = isset($t['function']) ? $t['function'] : 'unknown';
        $output .= "  #$i $file:$line → $function()\n";
    }
    
    error_log($output);
}

/**
 * Check if debug mode is enabled
 */
function is_debug_mode() {
    return defined('DEBUG_MODE') && DEBUG_MODE === true;
}

/**
 * Debug only if debug mode is enabled
 */
function debug_if($data, $title = 'Debug') {
    if (is_debug_mode()) {
        dump($data, $title);
    }
}

