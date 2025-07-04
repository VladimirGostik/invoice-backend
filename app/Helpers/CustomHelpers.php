<?php

if (! function_exists('get_client_ip')) {
    /**
     * Get the client's IP address, considering X-Real-IP header.
     */
    function get_client_ip(): string
    {
        // Try to get the IP from X-Real-IP header, fallback to request()->ip() if null
        $xRealIp = request()->header('X-Real-IP');

        return $xRealIp ?? request()->ip();
    }
}
