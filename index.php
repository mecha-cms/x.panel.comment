<?php

if (!is_file($cache = LOT . D . 'cache' . D . 'comments.php')) {
    $comments = [];
    foreach (g(LOT . D . 'comment', 'archive,draft,page', true) as $k => $v) {
        $comments[pathinfo($k, PATHINFO_FILENAME)] = strtr($k, [LOT . D . 'comment' . D => "", D => '/']);
    }
    krsort($comments);
    file_put_contents($cache, '<?' . 'php return ' . z(array_slice(array_values($comments), 0, $_['chunk'] ?? 20)) . ';');
}