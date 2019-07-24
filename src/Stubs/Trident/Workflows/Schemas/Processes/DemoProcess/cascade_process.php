<?php

return [
    'index'   => [
        'initial_place' => 'draft',
        'transitions'   => [
            'to_review' => '<namespace><Class>@<function>',
            'publish' => '<namespace><Class>@<function>',
            'reject' => '<namespace><Class>@<function>'
        ],
    ]
];