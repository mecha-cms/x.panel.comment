<?php

// Disable this extension if `comment` extension is disabled or removed ;)
if (!isset($state->x->comment)) {
    return $_;
}

Hook::set('_', function($_) use($user) {
    if (isset($_['lot']['bar']['lot'][0]['lot']['folder']['lot']['comment'])) {
        $_['lot']['bar']['lot'][0]['lot']['folder']['lot']['comment']['icon'] = 'M17,12V3A1,1 0 0,0 16,2H3A1,1 0 0,0 2,3V17L6,13H16A1,1 0 0,0 17,12M21,6H19V15H6V17A1,1 0 0,0 7,18H18L22,22V7A1,1 0 0,0 21,6Z';
        if (is_file($cache = LOT . D . 'cache' . D . 'comments.php')) {
            [$comments_new, $comments] = array_replace([[], []], (array) require $cache);
            $_['lot']['bar']['lot'][0]['lot']['folder']['lot']['comment']['status'] = count($comments_new);
        }
    }
    return $_;
}, 0);

if (0 === strpos($_['path'] . '/', 'comment/') && !array_key_exists('type', $_GET) && !isset($_['type'])) {
    if (!empty($_['part']) && $_['folder']) {
        $_['type'] = 'pages/comment';
    } else if (empty($_['part']) && $_['file']) {
        $x = pathinfo($_['file'], PATHINFO_EXTENSION);
        if ('data' === $x) {
            $_['type'] = 'data';
        } else if (in_array($x, ['archive', 'draft', 'page'])) {
            $_['type'] = 'page/comment';
        }
    }
}

if (0 === strpos($_['type'] . '/', 'pages/comment/')) {
    Hook::set('_', function($_) {
        $_['deep'] = $is_root = 'comment' === $_['path']; // Enable recursive page list in root
        $_['sort'] = array_replace([-1, 'time'], (array) ($_GET['sort'] ?? [])); // Sort descending by `time` data by default
        if ($is_root) {
            $_['lot']['desk']['lot']['form']['lot'][0]['lot']['tasks']['skip'] = true; // Hide create comment button in root
            $count = 0;
            if (is_file($cache = LOT . D . 'cache' . D . 'comments.php')) {
                [$comments_new, $comments] = array_replace([[], []], (array) require $cache);
                $count = count($comments_new);
            }
            $_['lot']['desk']['lot']['form']['lot'][0]['description'] = ['There ' . (1 === $count ? 'is' : 'are') . ' ' . (0 === $count ? 'no' : '%d') . ' unread comment' . (1 === $count ? "" : 's') . '.', [$count]];
            $_['lot']['desk']['lot']['form']['lot'][0]['title'] = ['Recent %s', ['Comments']];
        }
        return $_;
    }, 9.9);
    Hook::set('_', function($_) {
        if (!empty($_['lot']['desk']['lot']['form']['lot'][0]['lot']['tasks']['lot']['data'])) {
            // Hide create data button
            $_['lot']['desk']['lot']['form']['lot'][0]['lot']['tasks']['lot']['data']['skip'] = true;
        }
        if (
            !empty($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['pages']['lot']['pages']['lot']) &&
            !empty($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['pages']['lot']['pages']['type']) &&
            'pages' === $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['pages']['lot']['pages']['type']
        ) {
            extract($GLOBALS, EXTR_SKIP);
            if (is_file($cache = LOT . D . 'cache' . D . 'comments.php')) {
                [$comments_new, $comments] = array_replace([[], []], (array) require $cache);
                $comments_new = array_flip($comments_new);
            }
            foreach ($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['pages']['lot']['pages']['lot'] as $k => &$v) {
                $p = new Comment($k);
                $pp = new Comment($k);
                $parent_count = 0;
                $parent_max = $state->x->comment->page->deep ?? 0;
                $avatar = $p->avatar(72) ?? null;
                $description = To::description(x\panel\to\w($p->content ?? ""));
                $title = x\panel\to\w((string) $p->author ?? "");
                while ($parent = $pp['parent']) {
                    ++$parent_count;
                    if (!is_file($file = dirname($pp->path) . D . $parent . '.page')) {
                        break;
                    }
                    $pp = new Comment($file);
                }
                $v['current'] = isset($comments_new[strtr($k, [LOT . D . 'comment' . D => "", D => '/'])]);
                $v['description'] = $description;
                $v['image'] = $avatar;
                $v['link'] = $p->url;
                $v['title'] = $title;
                $v['url'] = false;
                $v['tasks']['reply'] = [
                    'active' => $can_reply = $parent_count < $parent_max,
                    'description' => $can_reply ? ['Reply to %s', $title] : null,
                    'icon' => 'M10,9V5L3,12L10,19V14.9C15,14.9 18.5,16.5 21,20C20,15 17,10 10,9Z',
                    'url' => $can_reply ? [
                        'part' => 0,
                        'path' => strtr($p->page->url, [$url . '/' => 'comment/']),
                        'query' => [
                            'parent' => $p->name,
                            'type' => 'page/comment'
                        ],
                        'task' => 'set'
                    ] : null,
                    'stack' => 9.9
                ];
                // Disable page children feature
                $v['tasks']['enter']['skip'] = true;
                $v['tasks']['set']['skip'] = true;
            }
            unset($v);
        }
        $_['lot']['desk']['lot']['form']['lot'][0]['lot']['tasks']['lot']['page']['description'][1] = 'Comment';
        $_['lot']['desk']['lot']['form']['lot'][0]['lot']['tasks']['lot']['page']['title'] = 'Comment';
        $_['lot']['desk']['lot']['form']['lot'][0]['lot']['tasks']['lot']['page']['url'] = [
            'part' => 0,
            'query' => [
                'chunk' => null,
                'deep' => null,
                'query' => null,
                'stack' => null,
                'tab' => null,
                'type' => 'page/comment',
                'x' => null
            ],
            'task' => 'set'
        ];
        return $_;
    }, 10.1);
} else if (0 === strpos($_['type'] . '/', 'page/comment/')) {
    if (empty($page) || is_object($page) && !$page->exist) {
        $page = new Comment($_['file']);
    }
    $parent = $_GET['parent'] ?? null;
    // Make `parent` query to be unset by default
    unset($_GET['parent'], $GLOBALS['_']['query']['parent'], $_['query']['parent']);
    Hook::set('_', function($_) use($page, $parent) {
        $parent = $parent ?? $page['parent'];
        if ($file = $_['file']) {
            $folder = dirname($file);
            $comment_ref = $parent && is_file($p = $folder . D . $parent . '.page') ? new Comment($p) : new Comment;
            $page_ref = strtr($folder, [
                LOT . D . 'comment' . D => LOT . D . 'page' . D
            ]);
            $page_ref = new Page(exist([
                $page_ref . '.archive',
                $page_ref . '.page'
            ], 1) ?: null);
        } else if ($folder = $_['folder']) {
            $comment_ref = $parent && is_file($p = $folder . D . $parent . '.page') ? new Comment($p) : new Comment;
            $page_ref = strtr($folder, [
                LOT . D . 'comment' . D => LOT . D . 'page' . D
            ]);
            $page_ref = new Page(exist([
                $page_ref . '.archive',
                $page_ref . '.page'
            ], 1) ?: null);
        }
        $_['lot'] = array_replace_recursive($_['lot'] ?? [], [
            'bar' => [
                // `bar`
                'lot' => [
                    // `links`
                    0 => [
                        'lot' => [
                            'set' => [
                                'description' => ['New %s', 'Comment'],
                                'icon' => 'M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22H9M11,6V9H8V11H11V14H13V11H16V9H13V6H11Z',
                                'skip' => 'set' === $_['task'] || !$page_ref->exist, // Skip only if parent folder does not relate to any page
                                'url' => [
                                    'part' => 0,
                                    'path' => dirname($_['path']),
                                    'query' => [
                                        'chunk' => null,
                                        'deep' => null,
                                        'query' => null,
                                        'stack' => null,
                                        'tab' => null,
                                        'type' => 'page/comment',
                                        'x' => null
                                    ],
                                    'task' => 'set'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'desk' => [
                // `desk`
                'lot' => [
                    'form' => [
                        // `form/post`
                        'lot' => [
                            0 => 'set' === $_['task'] && $comment_ref->exist ? [
                                // `section`
                                'title' => ['Reply to %s', ['<a href="' . x\panel\to\link([
                                    'path' => $_['path'] . '/' . $comment_ref->name . '.page',
                                    'query' => [
                                        'parent' => null
                                    ],
                                    'task' => 'get'
                                ]) . '" rel="nofollow" target="_blank">' . $comment_ref->author . '</a>']],
                                'content' => $comment_ref->content,
                                'description' => (string) $comment_ref->time
                            ] : ('set' === $_['task'] && $page_ref->exist ? [
                                // `section`
                                'title' => ['Comment to %s', ['<a href="' . x\panel\to\link([
                                    'path' => 'page/' . substr($_['path'], strlen('comment/')) . '.' . $page_ref->x,
                                    'query' => [
                                        'parent' => null,
                                        'type' => null
                                    ],
                                    'task' => 'get'
                                ]) . '" rel="nofollow" target="_blank">' . $page_ref->title . '</a>']],
                                'content' => $page_ref->content,
                                'description' => (string) $page_ref->time
                            ] : []),
                            1 => [
                                // `section`
                                'lot' => [
                                    'tabs' => [
                                        // `tabs`
                                        'lot' => [
                                            'page' => [
                                                'lot' => [
                                                    'fields' => [
                                                        // `fields`
                                                        'lot' => [
                                                            'author' => $comment_ref->exist ? [] : [
                                                                'focus' => true,
                                                                'stack' => 10,
                                                                'type' => 'title',
                                                                'width' => true
                                                            ],
                                                            'content' => ['focus' => $comment_ref->exist],
                                                            'description' => ['skip' => true],
                                                            'name' => ['skip' => true],
                                                            'tags' => ['skip' => true],
                                                            'title' => ['skip' => true]
                                                        ]
                                                    ]
                                                ],
                                                'stack' => 10,
                                                'value' => 'comment'
                                            ],
                                            'data' => [
                                                'lot' => [
                                                    'fields' => [
                                                        'lot' => [
                                                            'parent' => [
                                                                'hint' => date('Y-m-d-H-i-s'),
                                                                'name' => 'data[parent]',
                                                                'pattern' => "^[1-9]\\d{3,}-(0\\d|1[0-2])-(0\\d|[1-2]\\d|3[0-1])-([0-1]\\d|2[0-4])(-([0-5]\\d|60)){2}$",
                                                                'stack' => 10,
                                                                'type' => 'name',
                                                                'value' => $parent ? (new Time($parent))->name : null
                                                            ],
                                                            'time' => ['skip' => true]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'values' => [
                            'page' => ['name' => $page->name]
                        ]
                    ]
                ]
            ]
        ]);
        return $_;
    }, 10.1);
    if ($_['file'] && is_file($cache = LOT . D . 'cache' . D . 'comments.php')) {
        [$comments_new, $comments] = array_replace([[], []], (array) require $cache);
        $comments_new = array_flip($comments_new);
        unset($comments_new[substr($_['path'], strlen('comment/'))]); // Mark as read!
        file_put_contents($cache, '<?' . 'php return' . z([array_keys($comments_new), $comments]) . ';');
    }
}

return $_;