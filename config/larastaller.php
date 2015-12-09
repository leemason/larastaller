<?php
return [
    'application_name' => 'Application',
    'installer_logo' => '',
    'requirements' => [
        \LeeMason\Larastaller\Requirements\PhpVersionRequirement::class,
        \LeeMason\Larastaller\Requirements\PdoRequirement::class,
        \LeeMason\Larastaller\Requirements\MbStringRequirement::class,
        \LeeMason\Larastaller\Requirements\OpenSSLRequirement::class,
        \LeeMason\Larastaller\Requirements\TokenizerRequirement::class,
        \LeeMason\Larastaller\Requirements\FolderPermissionsRequirement::class,
        \LeeMason\Larastaller\Requirements\EnvFileRequirement::class,
    ],
    'versions' => [
        
    ]
];