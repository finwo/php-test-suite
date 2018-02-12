<?php

namespace Finwo\TestSuite;

class Test {
    protected $depth = 0;

    protected function success() {
        if ( $this->depth === 0 ) echo PHP_EOL, '  ';
        echo '.';
        flush();
        $this->depth = ($this->depth+1)%20;
    }

    protected function fail( $failure = null ) {
        if ( $this->depth === 0 ) echo PHP_EOL, '  ';
        echo 'F';
        flush();
        $this->depth = ($this->depth+1)%20;
        array_push(Suite::$fails, $failure);
    }

    public function __construct( $name ) {
        Suite::$tests++;
        echo PHP_EOL, PHP_EOL, $name, ':';
    }

    /**
     * @param mixed  $a
     * @param mixed  $b
     * @param string $errorMessage
     *
     * @return Test
     */
    public function assert( $a, $b, $errorMessage = null ) {
        Suite::$asserts++;
        $result = ( $a === $b );
        if ( $result ) {
            $this->success();
        } else {
            $this->fail(array('assert', $a, $b, $errorMessage));
        }
        return $this;
    }

    /**
     * @param mixed  $a
     * @param mixed  $b
     * @param string $errorMessage
     *
     * @return Test
     */
    public function assertNot( $a, $b, $errorMessage = null ) {
        Suite::$asserts++;
        $result = ( $a !== $b );
        if ( $result ) {
            $this->success();
        } else {
            $this->fail(array('assertNot', $a, $b, $errorMessage));
        }
        return $this;
    }

    /**
     * @param mixed  $a
     * @param mixed  $b
     * @param string $errorMessage
     *
     * @return Test
     */
    public function assertContains( $a, $b, $errorMessage = null ) {
        Suite::$asserts++;
        $result = strpos($b, $a) !== false;
        if ( $result ) {
            $this->success();
        } else {
            $this->fail(array('assertContains', $a, $b, $errorMessage));
        }
        return $this;
    }
}
