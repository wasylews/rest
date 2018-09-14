<?php

namespace Core\Web;


class UrlMatcher {

    public static function match($pattern, $url): array {
        $matches = [];
        if (!preg_match('/' . $pattern . '/', $url, $matches)) {
            return null;
        }
        // we need only named groups
        foreach($matches as $key => $match) {
            if (is_int($key)) {
                unset($matches[$key]);
            }
        }
        return $matches;
    }

}