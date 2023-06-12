<?php

if (is_file($cache = LOT . D . 'cache' . D . 'comments.php')) {
    // Shift data on every comment create
    Hook::set('on.comment.set', function ($file) use ($cache) {
        $comment = strtr($file, [LOT . D . 'comment' . D => "", D => '/']);
        [$comments_new, $comments] = array_replace([[], []], (array) require $cache);
        $comments_new[] = $comment;
        $comments = array_slice(array_merge([$comment], array_values($comments)), 0, 20);
        file_put_contents($cache, '<?' . 'php return' . z([$comments_new, $comments]) . ';');
    });
} else {
    $comments = [];
    foreach (g(LOT . D . 'comment', 'archive,draft,page', true) as $k => $v) {
        $comments[pathinfo($k, PATHINFO_FILENAME)] = strtr($k, [LOT . D . 'comment' . D => "", D => '/']);
    }
    krsort($comments);
    $comments = array_slice(array_values($comments), 0, 20);
    if (!is_dir($folder = dirname($cache))) {
        mkdir($folder, 0775, true);
    }
    file_put_contents($cache, '<?' . 'php return' . z([[], $comments]) . ';');
}