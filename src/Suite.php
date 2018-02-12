<?php

namespace Finwo\TestSuite;

class Suite {
    public static $tests   = 0;
    public static $asserts = 0;
    public static $fails   = array();
    public static $appRoot = null;

    /**
     * Builds the static approot for our suite
     */
    public static function ensureApproot() {
        if (is_string(self::$appRoot)) return;
        if (defined('APPROOT')) {
            self::$appRoot = APPROOT;
            return;
        }
        $thisPath      = realpath(dirname(__FILE__));
        self::$appRoot = $thisPath;
        $vendorCount = substr_count($thisPath,'/vendor/');
        while( substr_count($thisPath,'/vendor/') == $vendorCount ) {
            self::$appRoot = dirname(self::$appRoot);
        }
    }

    /**
     * Create a new test
     *
     * @param string $name
     *
     * @return Test|null
     */
    public static function test( $name ) {
        self::ensureApproot();
        if(!is_string($name)) return null;
        if (!self::$tests) {

            // Init message
            $composerData = json_decode(file_get_contents(self::$appRoot.DIRECTORY_SEPARATOR.'composer.json'));
            echo PHP_EOL;
            echo 'Testing environment for ', $composerData->name, PHP_EOL;
            echo '------------------------', str_repeat('-', strlen($composerData->name)), PHP_EOL;

            // Shutdown message
            register_shutdown_function(function() {
                printf(PHP_EOL . PHP_EOL . 'Ran %d tests with %d assertions of which %d failed' . PHP_EOL . PHP_EOL, Suite::$tests, Suite::$asserts, count(Suite::$fails));

                foreach (Suite::$fails as $index => $fail) {
                    printf('Error #%d: %s' . PHP_EOL, $index+1, array_pop($fail));
                    switch(array_shift($fail)) {
                        case 'assert':
                            $a = array_pop($fail);
                            $b = array_pop($fail);
                            if(is_array($a)) $a = json_encode($a);
                            if(is_array($b)) $b = json_encode($b);
                            printf('  "%s" doesn\'t match "%s"' . PHP_EOL, $a, $b );
                            break;
                        case 'assertNot':
                            printf('  "%s" matches "%s"' . PHP_EOL, array_pop($fail), array_pop($fail));
                            break;
                        case 'assertContains':
                            printf('  "%s" doesn\'t contain "%s"'.PHP_EOL, array_pop($fail), array_pop($fail));
                            break;
                        default:
                            break;
                    }
                    print("\n");
                }
                exit(count(Suite::$fails));
            });

        }
        return new Test( $name );
    }

    /**
     * @param array  $input
     * @param string $parentKey
     *
     * @return array
     */
    public static function array_flatten( $input = array(), $parentKey = '' ) {
        $output = array();
        foreach ($input as $key => $value) {
            $compositeKey = strlen($parentKey) ? $parentKey.'.'.$key : $key;
            switch(gettype($value)) {
                case 'array':
                    $output = array_merge($output, self::array_flatten($value, $compositeKey));
                    break;
                case 'object':
                    // Skip
                    break;
                default:
                    $output[$compositeKey] = $value;
                    break;
            }
        }
        return $output;
    }

    // See: http://stackoverflow.com/a/24784144/2928176
    public static function scandir_recursive( $dir, &$results = array() ) {
        $files = scandir($dir);
        $results[] = realpath($dir);
        end($results);
        $popKey = key($results);
        foreach($files as $key => $value){
            if ( substr($value,0,1) == "." ) continue;
            $path = realpath($dir.DS.$value);
            if ( in_array($path,$results) ) continue; // Prevents infinite loops
            if(!is_dir($path)) {
                $results[] = $path;
            } else {
                self::scandir_recursive($path, $results);
            }
        }
        unset($results[$popKey]);
        return $results;
    }
}
