<?php

namespace j0hnys\Trident\Base\Constants\Trident;

use j0hnys\Trident\Base\Definition\Definition;

class FolderStructure extends Definition
{
    const base_path = 'T::string()';

    const schema = [
        '{{base_path}}' => [
            'database' => [
                'factories' => [
                    'Models' => 'T::string()',
                ],
            ],
        ],
        "{{base_path}}/database/generated_migrations/",
        // "{{base_path}}/database/generated_model_exports/",
        // "{{base_path}}/database/generated_models/",
        // "{{base_path}}/database/factories/Models/T::string()",
        // "{{base_path}}/app/Http/Controllers/Trident/T::string()",
        // "{{base_path}}/app/Http/Controllers/Trident/T::string()",
        // "{{base_path}}/app/Http/Controllers/Trident/T::string()",
        // "{{base_path}}/app/Models/",
        // "{{base_path}}/app/Models/T::string()",
        // "{{base_path}}/app/Models/Schemas/Exports/",
        // "{{base_path}}/app/Providers/TridentAuthServiceProvider.php",
        // "{{base_path}}/app/Providers/TridentServiceProvider.php",
        // "{{base_path}}/app/Providers/TridentRouteServiceProvider.php",
        // "{{base_path}}/app/Providers/TridentEventServiceProvider.php",
        // "{{base_path}}/app/Policies/Trident/T::string()",
        // "{{base_path}}/app/Trident/Base/Typed",
        // "{{base_path}}/app/Trident/Base/Processes",
        // "{{base_path}}/app/Trident/Base/Exceptions/DbRepositoryException.php",
        // "{{base_path}}/app/Trident/Base/Interfaces/DbRepositoryInterface.php",
        // "{{base_path}}/app/Trident/Base/Repositories/DbRepository.php",
        // "{{base_path}}/app/Trident/Interfaces/Workflows/Logic",
        // "{{base_path}}/app/Trident/Interfaces/Business/Logic",
        // "{{base_path}}/app/Trident/Interfaces/Business/Logic/T::string()",
        // "{{base_path}}/app/Trident/Interfaces/Workflows/Logic/T::string()",
        // "{{base_path}}/app/Trident/Interfaces/Workflows/Repositories/T::string()",
        // "{{base_path}}/app/Trident/Business/Logic",
        // "{{base_path}}/app/Trident/Business/Logic/T::string()",
        // "{{base_path}}/app/Trident/Business/Exceptions/T::string()",
        // "{{base_path}}/app/Trident/Workflows/Processes/{{td_entity_name}}",
        // "{{base_path}}/app/Trident/Workflows/Logic/T::string()",
        // "{{base_path}}/app/Trident/Workflows/Exceptions/T::string()",
        // "{{base_path}}/app/Trident/Workflows/Repositories/T::string()",
        // "{{base_path}}/app/Trident/Workflows/Validations/T::string()",
        // "{{base_path}}/app/Trident/Workflows/Logic",
        // "{{base_path}}/app/Trident/{{workflow_or_business}}/Events/Triggers/T::string()",
        // "{{base_path}}/app/Trident/{{workflow_or_business}}/Events/Listeners/T::string()",
        // "{{base_path}}/app/Trident/{{workflow_or_business}}/Events/Subscribers/T::string()",
        // "{{base_path}}/app/Trident/Interfaces/Workflows/Processes/{{td_entity_name}}",
        // "{{base_path}}/app/Trident/{{workflow_or_business}}/Schemas/Logic/{{td_entity_name}}/Resources/T::string()",
        // "{{base_path}}/app/Trident/{{workflow_or_business}}/Schemas/Logic/{{td_entity_name}}/Typed/T::string()",
        // "{{base_path}}/app/Trident/{{workflow_or_business}}/Schemas/Logic/{{td_entity_name}}/Typed/T::string()",
        // "{{base_path}}/routes/trident.php",
        // "{{base_path}}/tests/Trident/Business/Logic/T::string()",
        // "{{base_path}}/tests/Trident/Workflows/Logic/T::string()",
    ];

    const workflow_or_business = [
        'Workflow', 'Business'
    ];

    const td_entity_name = 'T::string()';

}

