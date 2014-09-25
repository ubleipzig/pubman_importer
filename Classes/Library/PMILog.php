<?php
namespace LeipzigUniversityLibrary\PubmanImporter\Library;

/**
 * Activity logging class
 *
 */
class PMILog {

    private static $logfile = "log.txt";

    /**
     * Appends message to log
     * @param string $message Message to append to log
     */
    public static function push($message) {
        self::stdout(self::timestamp() . ': ' . $message);
    }

    /**
     * Appends debug message to log
     * @param string $message Message to append to log
     */
    public static function debug($message) {
        if (PMI_DEBUG) {
            self::stdout(self::timestamp() . ' [DEBUG]: ' . $message);
        }
    }

    /**
     * Returns timestamp in log format
     *
     * @return string Formated human readable timestamp
     */
    private static function timestamp() {
        $current = microtime(TRUE);
        return '[' . date('Y-m-d\TH:i:s', floor($current)) . number_format($current - floor($current), 3) . 'Z]';
    }

    /**
     * Puts the data to defined output stream
     *
     * @param string @data String for output
     */
    private static function stdout($data) {
        // For first just to file...
        if (!empty($data)) {
            $fh = fopen(PMI_DIR . PMILog::$logfile, 'a');
            if (flock($fh, LOCK_EX | LOCK_NB)) {
                fputs($fh, $data . "\n", strlen($data) + 1);
                flock($fh, LOCK_UN | LOCK_NB);
                fclose($fh);
            } else {
                fclose($fh);
                return FALSE;
            }
        }
    }

    /**
     * Log rotation
     *
     */
    public static function rotate() {
        if (file_exists(PMI_DIR . self::$logfile) == False) {
            touch(PMI_DIR . self::$logfile);
        }
        $logsize = filesize(PMI_DIR . self::$logfile);
        if ($logsize > PMI_MAX_LOG_SIZE) {
            $src = fopen(PMI_DIR . self::$logfile, "r");
            $dst = fopen(PMI_DIR . self::$logfile . '.tmp', "w+");
            fputs($dst, "[... LOG ROTATION ...]\n");
            fseek($src, $logsize - PMI_MAX_LOG_SIZE);
            $firstLine = TRUE;
            while ($buffer = fread($src, 1024)) {
                if ($firstLine) {
                    $buffer = substr($buffer, strpos($buffer, "\n"), strlen($buffer));
                    $firstLine = FALSE;
                }
                fwrite($dst, $buffer, strlen($buffer));
            }
            fclose($src);
            fclose($dst);
            unlink(PMI_DIR . self::$logfile);
            rename(PMI_DIR . self::$logfile . '.tmp', PMI_DIR . self::$logfile);
        }
    }

    /**
     * Deletes the current logfile and recreates it
     */
    public static function removeLog() {
        unlink(PMI_DIR . self::$logfile);
        touch(PMI_DIR . self::$logfile);
    }

}

// END
?>
