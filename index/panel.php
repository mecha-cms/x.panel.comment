<?php

// Disable this extension if `comment` extension is disabled or removed ;)
if (!isset($state->x->comment)) {
    return $_;
}

if ('POST' === $_SERVER['REQUEST_METHOD']) {
    if (0 === strpos(($_POST['type'] ?? P) . '/', 'page/page/') && !isset($_POST['page']['state']['x']['comment'])) {
        // Set value to default if user does not check the toggle
        $_POST['page']['state']['x']['comment'] = (int) ($state->x->comment->status ?? 1);
    }
}

$count = 0;
if (is_file($f = LOT . D . 'cache' . D . 'comments.php')) {
    [$comments_new, $comments] = array_replace([[], []], (array) require $f);
    $count = count($comments_new);
}

if (isset($_['lot']['bar']['lot'][0]['lot']['folder']['lot']['comment'])) {
    $_['lot']['bar']['lot'][0]['lot']['folder']['lot']['comment']['icon'] = 'M17,12V3A1,1 0 0,0 16,2H3A1,1 0 0,0 2,3V17L6,13H16A1,1 0 0,0 17,12M21,6H19V15H6V17A1,1 0 0,0 7,18H18L22,22V7A1,1 0 0,0 21,6Z';
    if ($count) {
        $_['lot']['bar']['lot'][0]['lot']['folder']['lot']['comment']['status'] = $count;
    }
}

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

if (0 === strpos($_['type'] . '/', 'page/comment/')) {
    if (!$page->exist) {
        $page = new Page($_['file']);
    }
    $parent = $_['query']['parent'] ?? null;
    // Make `parent` query to be unset by default
    unset($_GET['parent'], $GLOBALS['_']['query']['parent'], $_['query']['parent']);
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
    $_['lot']['bar']['lot'][0]['lot']['set']['description'][1] = 'Comment';
    $_['lot']['bar']['lot'][0]['lot']['set']['icon'] = 'M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22H9M11,6V9H8V11H11V14H13V11H16V9H13V6H11Z';
    $_['lot']['bar']['lot'][0]['lot']['set']['skip'] = 'set' === $_['task'] || !$page_ref->exist; // Skip only if parent folder does not relate to any page
    $_['lot']['bar']['lot'][0]['lot']['set']['url'] = [
        'part' => 0,
        'path' => dirname($_['path']),
        'query' => x\panel\_query_set(['type' => 'page/comment']),
        'task' => 'set'
    ];
    $_['lot']['desk']['lot']['form']['lot'][0] = 'set' === $_['task'] && $comment_ref->exist ? [
        'title' => ['Reply to %s', ['<a href="' . x\panel\to\link([
            'path' => $_['path'] . '/' . $comment_ref->name . '.page',
            'query' => ['parent' => null],
            'task' => 'get'
        ]) . '" rel="nofollow" target="_blank">' . $comment_ref->author . '</a>']],
        'content' => '<div class="p">' . $comment_ref->content . '</div>',
        'description' => (string) $comment_ref->time
    ] : ('set' === $_['task'] && $page_ref->exist ? [
        'title' => ['Comment to %s', ['<a href="' . x\panel\to\link([
            'path' => 'page/' . substr($_['path'], strlen('comment/')) . '.' . $page_ref->x,
            'query' => [
                'parent' => null,
                'type' => null
            ],
            'task' => 'get'
        ]) . '" rel="nofollow" target="_blank">' . $page_ref->title . '</a>']],
        'content' => '<div class="p">' . $page_ref->content . '</div>',
        'description' => (string) $page_ref->time
    ] : []);
    $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['page']['lot']['fields']['lot']['author'] = [
        'focus' => true,
        'stack' => 10,
        'type' => $comment_ref->exist ? 'hidden' : 'title',
        'value' => $_['author'] ?? null,
        'width' => true
    ];
    $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['page']['lot']['fields']['lot']['content']['focus'] = $comment_ref->exist;
    $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['page']['lot']['fields']['lot']['description']['skip'] = true;
    $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['page']['lot']['fields']['lot']['name']['skip'] = true;
    $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['page']['lot']['fields']['lot']['tags']['skip'] = true;
    $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['page']['lot']['fields']['lot']['title']['skip'] = true;
    $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['page']['value'] = 'comment';
    $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['data']['lot']['fields']['lot']['email'] = [
        'name' => 'page[email]',
        'stack' => 10,
        'type' => 'email',
        'value' => 'set' === $_['task'] ? ($state->email ?? null) : ($page['email'] ?? null)
    ];
    $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['data']['lot']['fields']['lot']['parent'] = [
        'hint' => date('Y-m-d-H-i-s'),
        'name' => 'data[parent]',
        'pattern' => "[1-9]\\d{3,}-(0\\d|1[0-2])-(0\\d|[1-2]\\d|3[0-1])-([0-1]\\d|2[0-4])(-([0-5]\\d|60)){2}",
        'stack' => 20,
        'type' => 'name',
        'value' => $parent ? (new Time($parent))->name : null
    ];
    $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['data']['lot']['fields']['lot']['time']['skip'] = true;
    $_['lot']['desk']['lot']['form']['values']['page']['name'] = $page->name;
    if ($file && is_file($f = LOT . D . 'cache' . D . 'comments.php')) {
        [$comments_new, $comments] = array_replace([[], []], (array) require $f);
        $comments_new = array_flip($comments_new);
        unset($comments_new[substr($_['path'], strlen('comment/'))]); // Mark as read!
        file_put_contents($f, '<?' . 'php return' . z([array_keys($comments_new), $comments]) . ';');
    }
    return $_;
}

if (0 === strpos($_['type'] . '/', 'pages/comment/')) {
    if ($_['deep'] = $_['query']['deep'] ?? 'comment' === $_['path']) {
        $_['lot']['desk']['lot']['form']['lot'][0]['lot']['tasks']['skip'] = true; // Hide create comment button in root folder
        $_['lot']['desk']['lot']['form']['lot'][0]['description'] = ['There ' . (1 === $count ? 'is' : 'are') . ' ' . (0 === $count ? 'no' : '%d') . ' unread comment' . (1 === $count ? "" : 's') . '.', [$count]];
        $_['lot']['desk']['lot']['form']['lot'][0]['title'] = ['Recent %s', ['Comments']];
        if (!empty($_['lot']['desk']['lot']['form']['lot'][0]['lot']['tasks']['lot']['data'])) {
            $_['lot']['desk']['lot']['form']['lot'][0]['lot']['tasks']['lot']['data']['skip'] = true; // Hide create data button
        }
        if (!empty($_['lot']['desk']['lot']['form']['lot'][0]['lot']['tasks']['lot']['parent'])) {
            // Make directory level navigation points to the parent page
            $folder = strtr($_['folder'], [
                LOT . D . 'comment' . D => LOT . D . 'page' . D
            ]);
            if ($file = exist([
                $folder . '.archive',
                $folder . '.page'
            ], 1)) {
                $_['lot']['desk']['lot']['form']['lot'][0]['lot']['tasks']['lot']['parent']['description'] = ['Open %s', 'Page'];
                $_['lot']['desk']['lot']['form']['lot'][0]['lot']['tasks']['lot']['parent']['icon'] = 'M14,3V5H17.59L7.76,14.83L9.17,16.24L19,6.41V10H21V3M19,19H5V5H12V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V12H19V19Z';
                $_['lot']['desk']['lot']['form']['lot'][0]['lot']['tasks']['lot']['parent']['link'] = [
                    'part' => 0,
                    'path' => strtr($file, [
                        LOT . D => "",
                        D => '/'
                    ]),
                    'query' => x\panel\_query_set(),
                    'task' => 'get'
                ];
                unset($_['lot']['desk']['lot']['form']['lot'][0]['lot']['tasks']['lot']['parent']['url']);
            }
        }
    }
    $_['sort'] = array_replace([-1, 'time'], (array) ($_['query']['sort'] ?? [])); // Sort descending by `time` data by default
    $_['lot']['desk']['lot']['form']['lot'][0]['lot']['tasks']['lot']['page']['description'][1] = 'Comment';
    $_['lot']['desk']['lot']['form']['lot'][0]['lot']['tasks']['lot']['page']['title'] = 'Comment';
    $_['lot']['desk']['lot']['form']['lot'][0]['lot']['tasks']['lot']['page']['url'] = [
        'part' => 0,
        'query' => x\panel\_query_set(['type' => 'page/comment']),
        'task' => 'set'
    ];
    Hook::set('_', function ($_) use ($comments_new, $state) { // Late override
        if (empty($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['pages']['lot']['pages']['lot'])) {
            return $_;
        }
        if (!isset($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['pages']['lot']['pages']['type'])) {
            return $_;
        }
        if (0 !== strpos($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['pages']['lot']['pages']['type'] . '/', 'pages/')) {
            return $_;
        }
        $comments_k = array_flip($comments_new);
        foreach ($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['pages']['lot']['pages']['lot'] as $k => &$v) {
            $comment = new Comment($k);
            $parent_comment = $comment;
            $parent_count = 0;
            $parent_max = $state->x->comment->page->deep ?? 0;
            $avatar = $comment->avatar(72, 72, 100) ?? null;
            $description = To::description(x\panel\to\w($comment->content ?? ""));
            $title = x\panel\to\w((string) $comment->author ?? "");
            $x = $comment->x;
            while ($parent = $parent_comment['parent']) {
                ++$parent_count;
                if (!is_file($parent_file = dirname($parent_comment->path) . D . $parent . '.page')) {
                    break;
                }
                $parent_comment = new Comment($parent_file);
            }
            $v['current'] = isset($comments_k[strtr($k, [
                LOT . D . 'comment' . D => "",
                D => '/'
            ])]);
            $v['description'] = $description;
            $v['image'] = $avatar;
            $v['link'] = 'archive' === $x || 'draft' === $x ? false : $comment->url;
            $v['title'] = $title;
            $v['url'] = false;
            $v['tasks']['reply'] = [
                'active' => $can_reply = $parent_count < $parent_max && 'page' === $x,
                'description' => $can_reply ? ['Reply to %s', $title] : null,
                'icon' => 'M10,9V5L3,12L10,19V14.9C15,14.9 18.5,16.5 21,20C20,15 17,10 10,9Z',
                'url' => $can_reply ? [
                    'part' => 0,
                    'path' => substr(strtr($comment->page->path, [
                        LOT . D . 'page' . D => 'comment/',
                        D => '/'
                    ]), 0, -(strlen($comment->page->x) + 1)),
                    'query' => [
                        'parent' => $comment->name,
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
        return $_;
    }, 0);
}

$is_in_comment = 0 === strpos($_['path'] . '/', 'comment/');
$is_in_comment_guard = 0 === strpos($_['path'] . '/', 'x/comment.guard/');

if ($has_comment_guard = isset($state->x->{'comment.guard'})) {
    $names = [
        'author' => [
            'Author',
            'Prevent users from publishing comments based on certain words in their comment author name.',
            'Block comments from their author name, each separated by a line break.'
        ],
        'content' => [
            'Content',
            'Prevent users from publishing comments based on certain words in their comment content.',
            'Enter a list of words you want to block in comments, each separated by a line break.'
        ],
        'email' => [
            'Email',
            'Prevent users from publishing comments based on their email address.',
            'Block comments from their email address, each separated by a line break.'
        ],
        'i-p' => [
            'IP',
            'Prevent users from publishing comments based on their IP address.',
            'Block comments from their IP address, each separated by a line break.'
        ],
        'link' => [
            'Link',
            'Prevent users from publishing comments based on their link address.',
            'Block comments from their link address, each separated by a line break.'
        ]
    ];
}

if ($has_comment_guard && ($is_in_comment || $is_in_comment_guard) && isset($_['lot']['bar']['lot'][1]['lot'])) {
    $menus = [];
    $stack = 0;
    foreach ($names as $k => $v) {
        $menus[$k] = [
            'current' => 'x/comment.guard/' . $k . '.txt' === $_['path'],
            'description' => $v[1],
            'icon' => 'M19,20V22.97H17V20H14V18H17V15H19V18H22V20H19M21,11C21,11.9 20.9,12.78 20.71,13.65C20.13,13.35 19.5,13.15 18.81,13.05C18.93,12.45 19,11.83 19,11.22V6.3L12,3.18L5,6.3V11.22C5,15.54 8.25,20 12,21L12.31,20.91C12.5,21.53 12.83,22.11 13.22,22.62L12,23C6.84,21.74 3,16.55 3,11V5L12,1L21,5V11Z',
            ($is_in_comment_guard ? 'url' : 'link') => [
                'part' => 0,
                'path' => 'x/comment.guard/' . $k . '.txt',
                'query' => null,
                'task' => 'get'
            ],
            'stack' => $stack += 10,
            'title' => $v[0],
        ];
    }
    // Add “Guard” menu
    $_['lot']['bar']['lot'][1]['lot']['guard'] = [
        'current' => $is_in_comment_guard,
        'icon' => 'M12,12H19C18.47,16.11 15.72,19.78 12,20.92V12H5V6.3L12,3.19M12,1L3,5V11C3,16.55 6.84,21.73 12,23C17.16,21.73 21,16.55 21,11V5L12,1Z',
        'lot' => $menus,
        'stack' => 0,
        'url' => [
            'part' => 1,
            'path' => 'x/comment.guard',
            'query' => null,
            'task' => 'get'
        ]
    ];
}

if ($has_comment_guard && $is_in_comment_guard && 'get' === $_['task']) {
    Hook::set('_', function ($_) use ($names) { // Late override
        if (0 !== strpos($_['type'] . '/', 'file/')) {
            return $_;
        }
        if (!isset($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['file']['lot']['fields']['lot']['content'])) {
            return $_;
        }
        if (!$name = $names[$n = basename($_['file'] ?? "", '.txt')] ?? "") {
            return $_;
        }
        // This line removes the “Delete” button to prevent non-savvy user(s) from deleting the file
        if (isset($_['lot']['desk']['lot']['form']['lot'][2]['lot']['fields']['lot'][0]['lot']['tasks']['lot']['let'])) {
            $_['lot']['desk']['lot']['form']['lot'][2]['lot']['fields']['lot'][0]['lot']['tasks']['lot']['let']['skip'] = true;
        }
        // This line sets the `name` field as hidden field so that non-savvy user(s) cannot change the file name
        if (isset($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['file']['lot']['fields']['lot']['name'])) {
            $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['file']['lot']['fields']['lot']['name']['type'] = 'hidden';
        }
        // These lines add an explanation below the file content editor on how to edit the file
        if ($_['path'] === 'x/comment.guard/' . $n . '.txt') {
            $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['file']['lot']['fields']['lot']['content']['description'] = i($name[1]) . ' ' . i($name[2]);
            $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['file']['lot']['fields']['lot']['content']['focus'] = true;
        }
        return $_;
    }, 0);
}

Hook::set('_', function ($_) use ($has_comment_guard, $page) { // Late override
    if ($has_comment_guard && 0 === strpos($_['type'] . '/', 'files/x/')) {
        if (empty($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['files']['lot']['files']['lot'])) {
            return $_;
        }
        if (!isset($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['files']['lot']['files']['type'])) {
            return $_;
        }
        if (0 !== strpos($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['files']['lot']['files']['type'] . '/', 'files/')) {
            return $_;
        }
        // Hide `author.txt`, `content.txt`, `email.txt`, `i-p.txt`, and `link.txt` from the list as they are already accessible through the “Guard” menu
        foreach (['author', 'content', 'email', 'i-p', 'link'] as $v) {
            if (isset($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['files']['lot']['files']['lot'][$v = $_['folder'] . D . $v . '.txt'])) {
                $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['files']['lot']['files']['lot'][$v]['skip'] = true;
            }
        }
        // Update total file(s) count
        if (!isset($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['files']['lot']['pager']['count'])) {
            return $_;
        }
        if (!is_int($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['files']['lot']['pager']['count'])) {
            return $_;
        }
        $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['files']['lot']['pager']['count'] -= 5;
        return $_;
    }
    if (0 === strpos($_['type'] . '/', 'pages/page/')) {
        if (empty($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['pages']['lot']['pages']['lot'])) {
            return $_;
        }
        if (!isset($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['pages']['lot']['pages']['type'])) {
            return $_;
        }
        if (0 !== strpos($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['pages']['lot']['pages']['type'] . '/', 'pages/')) {
            return $_;
        }
        foreach ($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['pages']['lot']['pages']['lot'] as $k => &$v) {
            $count = q(g($folder = strtr(dirname($k) . D . pathinfo($k, PATHINFO_FILENAME), [
                LOT . D . 'page' . D => LOT . D . 'comment' . D
            ]), 'page'));
            $v['tasks']['comments'] = [
                'description' => ['%d Comment' . (1 === $count ? "" : 's'), $count],
                'icon' => 'M12,23A1,1 0 0,1 11,22V19H7A2,2 0 0,1 5,17V7A2,2 0 0,1 7,5H21A2,2 0 0,1 23,7V17A2,2 0 0,1 21,19H16.9L13.2,22.71C13,22.89 12.76,23 12.5,23H12M13,17V20.08L16.08,17H21V7H7V17H13M3,15H1V3A2,2 0 0,1 3,1H19V3H3V15M9,9H19V11H9V9M9,13H17V15H9V13Z',
                'link' => $count > 0 ? [
                    'part' => 1,
                    'path' => 'comment/' . strtr($folder, [
                        LOT . D . 'comment' . D => ""
                    ]),
                    'task' => 'get'
                ] : null,
                'stack' => 9.99
            ];
        }
        unset($v);
        return $_;
    }
    if (0 === strpos($_['type'] . '/', 'page/page/')) {
        if (!isset($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['state']['lot']['fields']['lot']['state.x'])) {
            return $_;
        }
        $comments = $page->comments ? ($page->comments->count ?? 0) : 0;
        $status = (int) ($state->x->comment->status ?? 1); // The default comment status
        if (!isset($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['state']['lot']['fields']['lot']['state.x']['values']['comment'])) {
            $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['state']['lot']['fields']['lot']['state.x']['values']['comment'] = (int) ($page->state['x']['comment'] ?? $status);
        }
        // Set option to close comment(s) if it is open by default or open comment(s) if it is close by default
        $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['state']['lot']['fields']['lot']['state.x']['lot']['comment'] = [
            'description' => 0 === $comments ? ['No %s yet.', ['comments']] : ['%d comment' . (1 === $comments ? "" : 's') . ' already published on this page.', [$comments]],
            'title' => [(0 === $comments ? (1 === $status ? 'Disable' : 'Enable') : (1 === $status ? 'Close' : 'Open')) . ' %s', ['comments']],
            'value' => 1 === $status ? (0 === $comments ? 0 : 2) : 1
        ];
    }
    return $_;
}, 0);

return $_;