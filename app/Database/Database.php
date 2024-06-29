<?php

function getDatabaseConfig(): array {
    return [
        "database" => [
            "test" => [
                "url" => "mysql:host=localhost:3306;dbname=php_login_management_tests",
                "username" => "root",
                "password" => ""
            ],
            "prod" => [
                "url" => "mysql:host=localhost:3306;dbname=php_login_management",
                "username" => "root",
                "password" => ""
            ]
        ]
    ];
}