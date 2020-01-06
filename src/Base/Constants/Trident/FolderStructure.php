<?php

namespace j0hnys\Trident\Base\Constants\Trident;

use j0hnys\Definitions\Definition;

final class FolderStructure extends Definition
{
    const schema = [
        'database' => [
            'factories' => [
                'something' => 'T::string()',
                'Models' => 'T::string()',
            ],
            'generated_migrations' => 'T::string()',
            'generated_model_exports' => 'T::string()',
            'generated_models' => 'T::string()',
        ],
        'app' => [
            'Http' => [
                'Controllers' => [
                    'Trident' => 'T::string()'
                ]
            ],
            'Models' => [
                'T::string()',
                'Schemas' => [
                    'Exports' => 'T::string()'
                ]
            ],
            'Providers' => [
                'TridentAuthServiceProvider.php',
                'TridentServiceProvider.php',
                'TridentRouteServiceProvider.php',
                'TridentEventServiceProvider.php',
            ],
            'Policies' => [
                'Trident' => 'T::string()',
            ],
            'Trident' => [
                'Base' => [
                    'Exceptions' => 'DbRepositoryException.php',
                    'Interfaces' => 'DbRepositoryInterface.php',
                    'Typed' => 'T::string()',
                    'Processes' => 'T::string()',
                    'Repositories' => 'DbRepository.php',
                ],
                'Interfaces' => [
                    'Business' => [
                        'Logic' => 'T::string()'
                    ],
                    'Workflows' => [
                        'Logic' => 'T::string()',
                        'Repositories' => 'T::string()',
                        'Processes' => 'T::string()',
                    ]
                ],
                'Business' => [
                    'Events' => [
                        'Triggers' => 'T::string()',
                        'Listeners' => 'T::string()',
                        'Subscribers' => 'T::string()',
                    ],
                    'Exceptions' => 'T::string()',
                    'Logic' => 'T::string()',
                    'Schemas' => [
                        'Logic' => [
                            '{{td_entity_name}}' => [
                                'Resources' => 'T::string()',
                                'Typed' => 'T::string()',
                            ]
                        ]
                    ]
                ],
                'Workflows' => [
                    'Events' => [
                        'Triggers' => 'T::string()',
                        'Listeners' => 'T::string()',
                        'Subscribers' => 'T::string()',
                    ],
                    'Exceptions' => 'T::string()',
                    'Logic' => 'T::string()',
                    'Processes' => [
                        '{{workflow_process}}' => 'T::string()',
                    ],
                    'Repositories' => 'T::string()',
                    'Validations' => 'T::string()',
                    'Schemas' => [
                        'Logic' => [
                            '{{td_entity_name}}' => [
                                'Resources' => 'T::string()',
                                'Typed' => 'T::string()',
                            ]
                        ]
                    ]
                ],
            ],
        ],
        'routes' => 'trident.php',
        'tests' => [
            'Trident' => [
                'Functional' => [
                    'Resource' => [
                        'Logic' => 'T::string()',
                    ],
                ],
                'Business' => [
                    'Logic' => 'T::string()',
                ],
                'Workflows' => [
                    'Logic' => 'T::string()',
                ],
            ]
        ],
    ];

    const base_path = 'T::string()';
    const workflow_or_business = [
        'Workflow', 'Business'
    ];
    const td_entity_name = 'T::string()';
    const workflow_process = 'T::string()';
}

