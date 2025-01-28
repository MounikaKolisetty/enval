<?php
class RateLimiter {
    private $capacity;
    private $tokens;
    private $rate;
    private $lastRequestTime;

    public function __construct($capacity, $rate) {
        $this->capacity = $capacity;
        $this->tokens = $capacity;
        $this->rate = $rate; // tokens per second
        $this->lastRequestTime = microtime(true);
    }

    private function addTokens() {
        $now = microtime(true);
        $timePassed = $now - $this->lastRequestTime;
        $this->lastRequestTime = $now;

        $tokensToAdd = $timePassed * $this->rate;
        $this->tokens = min($this->capacity, $this->tokens + $tokensToAdd);
    }

    public function allowRequest() {
        $this->addTokens();
        if ($this->tokens >= 1) {
            $this->tokens -= 1;
            return true;
        }
        return false;
    }
}
?>
