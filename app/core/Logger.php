<?php
class Logger
{
    const LOG_DIR = __DIR__ . '/../../storage/logs/';
    const LOG_FILE = 'app.log';

    protected static function getLogFile()
    {
        // Ensure log directory exists
        if (!is_dir(self::LOG_DIR)) {
            mkdir(self::LOG_DIR, 0777, true);
        }
        return self::LOG_DIR . self::LOG_FILE;
    }

    protected static function write($level, $message)
    {
        $date = date('Y-m-d H:i:s');
        $logLine = "[$date][$level] $message" . PHP_EOL;

        // Write the log to the file
        file_put_contents(self::getLogFile(), $logLine, FILE_APPEND);

        // Only output logs in CLI mode
        if (php_sapi_name() === 'cli') {
            echo $logLine;
        }
    }

    public static function info($message)
    {
        self::write('INFO', $message);
    }

    public static function error($message)
    {
        self::write('ERROR', $message);
    }

    public static function warning($message)
    {
        self::write('WARNING', $message);
    }

    public static function debug($message)
    {
        self::write('DEBUG', $message);
    }
}