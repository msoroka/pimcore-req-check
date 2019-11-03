<?php

namespace App\Requirements;

class Extensions
{
    /**
     * @return array
     * @throws \Exception
     */
    public static function check()
    {
        $checks = [];

        // check for memory limit
        $memoryLimit = ini_get('memory_limit');
        $memoryLimitState = Check::STATE_OK;
        $memoryLimitMessage = '';

        $checks[] = new Check([
            'name'    => 'memory_limit (in php.ini, minimum 512MB)',
            'link'    => 'http://www.php.net/memory_limit',
            'state'   => $memoryLimit,
            'message' => $memoryLimitMessage,
        ]);

        // pdo_mysql
        $checks[] = new Check([
            'name'  => 'PDO MySQL',
            'link'  => 'http://www.php.net/pdo_mysql',
            'state' => @constant('PDO::MYSQL_ATTR_FOUND_ROWS') ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // Mysqli
        $checks[] = new Check([
            'name'    => 'Mysqli',
            'link'    => 'http://www.php.net/mysqli',
            'state'   => class_exists('mysqli') ? Check::STATE_OK : Check::STATE_WARNING,
            'message' => "Mysqli can be used instead of PDO MySQL, though it isn't a requirement.",
        ]);

        // iconv
        $checks[] = new Check([
            'name'  => 'iconv',
            'link'  => 'http://www.php.net/iconv',
            'state' => function_exists('iconv') ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // dom
        $checks[] = new Check([
            'name'  => 'Document Object Model (DOM)',
            'link'  => 'http://www.php.net/dom',
            'state' => class_exists('DOMDocument') ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // simplexml
        $checks[] = new Check([
            'name'  => 'SimpleXML',
            'link'  => 'http://www.php.net/simplexml',
            'state' => class_exists('SimpleXMLElement') ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // gd
        $checks[] = new Check([
            'name'  => 'GD',
            'link'  => 'http://www.php.net/gd',
            'state' => function_exists('gd_info') ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // exif
        $checks[] = new Check([
            'name'  => 'EXIF',
            'link'  => 'http://www.php.net/exif',
            'state' => function_exists('exif_read_data') ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // multibyte support
        $checks[] = new Check([
            'name'  => 'Multibyte String (mbstring)',
            'link'  => 'http://www.php.net/mbstring',
            'state' => function_exists('mb_get_info') ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // file_info support
        $checks[] = new Check([
            'name'  => 'File Information (file_info)',
            'link'  => 'http://www.php.net/file_info',
            'state' => function_exists('finfo_open') ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // zip
        $checks[] = new Check([
            'name'  => 'zip',
            'link'  => 'http://www.php.net/zip',
            'state' => class_exists('ZipArchive') ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // gzip
        $checks[] = new Check([
            'name'  => 'zlib / gzip',
            'link'  => 'http://www.php.net/zlib',
            'state' => function_exists('gzcompress') ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // Intl
        $checks[] = new Check([
            'name'  => 'Intl',
            'link'  => 'http://www.php.net/intl',
            'state' => extension_loaded('intl') ? Check::STATE_OK : Check::STATE_ERROR,
        ]);

        // Locales
        if (extension_loaded('intl')) {
            $fmt = new \IntlDateFormatter('de', \IntlDateFormatter::FULL, \IntlDateFormatter::FULL, 'Europe/Vienna',
                \IntlDateFormatter::GREGORIAN, 'EEEE');
            $checks[] = new Check([
                'name'    => 'locales-all',
                'link'    => 'https://packages.debian.org/en/stable/locales-all',
                'state'   => ($fmt->format(new \DateTime('next tuesday')) == 'Dienstag') ? Check::STATE_OK : Check::STATE_WARNING,
                'message' => "It's recommended to have the GNU C Library locale data installed (eg. apt-get install locales-all).",
            ]);
        }

        // Imagick
        $checks[] = new Check([
            'name'  => 'Imagick',
            'link'  => 'http://www.php.net/imagick',
            'state' => class_exists('Imagick') ? Check::STATE_OK : Check::STATE_WARNING,
        ]);

        // APCu
        $checks[] = new Check([
            'name'    => 'APCu',
            'link'    => 'http://www.php.net/apcu',
            'state'   => (function_exists('apcu_fetch') && ini_get('apc.enabled')) ? Check::STATE_OK : Check::STATE_WARNING,
            'message' => "It's highly recommended to have the APCu extension installed and enabled.",
        ]);

        // OPcache
        $checks[] = new Check([
            'name'    => 'OPcache',
            'link'    => 'http://www.php.net/opcache',
            'state'   => function_exists('opcache_reset') ? Check::STATE_OK : Check::STATE_WARNING,
            'message' => "It's highly recommended to have the OPCache extension installed and enabled.",
        ]);

        // Redis
        $checks[] = new Check([
            'name'  => 'Redis',
            'link'  => 'https://pecl.php.net/package/redis',
            'state' => class_exists('Redis') ? Check::STATE_OK : Check::STATE_WARNING,
        ]);

        // curl for google api sdk
        $checks[] = new Check([
            'name'  => 'curl',
            'link'  => 'http://www.php.net/curl',
            'state' => function_exists('curl_init') ? Check::STATE_OK : Check::STATE_WARNING,
        ]);

        return $checks;
    }
}