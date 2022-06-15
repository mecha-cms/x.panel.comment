<?php

// Disable this extension if `comment` extension is disabled or removed ;)
if (!isset($state->x->comment)) {
    return $_;
}

Hook::set('_', function($_) use($user) {
    if (isset($_['lot']['bar']['lot'][0]['lot']['folder']['lot']['comment'])) {
        $_['lot']['bar']['lot'][0]['lot']['folder']['lot']['comment']['icon'] = 'M17,12V3A1,1 0 0,0 16,2H3A1,1 0 0,0 2,3V17L6,13H16A1,1 0 0,0 17,12M21,6H19V15H6V17A1,1 0 0,0 7,18H18L22,22V7A1,1 0 0,0 21,6Z';
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
        $_['deep'] = 'comment' === $_['path']; // Enable recursive page list in root
        return $_;
    }, 9.9);
    Hook::set('_', function($_) {
        if (
            !empty($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['pages']['lot']['pages']['lot']) &&
            !empty($_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['pages']['lot']['pages']['type']) &&
            'pages' === $_['lot']['desk']['lot']['form']['lot'][1]['lot']['tabs']['lot']['pages']['lot']['pages']['type']
        ) {
            extract($GLOBALS, EXTR_SKIP);
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
    Hook::set('_', function($_) use($page) {
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
                                'skip' => true, // TODO: Skip only if parent folder not related to a page file
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
                                                            'author' => [
                                                                'focus' => true,
                                                                'stack' => 10,
                                                                'type' => 'title',
                                                                'width' => true
                                                            ],
                                                            'description' => ['skip' => true],
                                                            'name' => ['skip' => true],
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
                                                                'value' => ($parent = $_GET['parent'] ?? $page['parent']) ? (new Time($parent))->name : null
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
                        ]
                    ]
                ]
            ]
        ]);
        return $_;
    }, 10.1);
}

return $_;