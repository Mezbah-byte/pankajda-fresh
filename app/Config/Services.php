<?php

namespace Config;

use CodeIgniter\Config\BaseService;

/**
 * Custom services container for the ERP.
 *
 * Register dependency-injected services here so the rest of the
 * application can resolve them via `service('name')` without any
 * hard `new` calls in controllers.
 */
class Services extends BaseService
{
    /**
     * JWT signing/verification service.
     */
    public static function jwt(bool $getShared = true): \App\Libraries\JwtService
    {
        if ($getShared) {
            return static::getSharedInstance('jwt');
        }
        return new \App\Libraries\JwtService();
    }

    /**
     * UUID generator.
     */
    public static function uuid(bool $getShared = true): \App\Libraries\UuidGenerator
    {
        if ($getShared) {
            return static::getSharedInstance('uuid');
        }
        return new \App\Libraries\UuidGenerator();
    }

    /**
     * Activity logger - records audit events.
     */
    public static function activityLogger(bool $getShared = true): \App\Libraries\ActivityLogger
    {
        if ($getShared) {
            return static::getSharedInstance('activityLogger');
        }
        return new \App\Libraries\ActivityLogger();
    }

    /**
     * Auth service - manages authentication and authorization.
     */
    public static function auth(bool $getShared = true): \App\Services\AuthService
    {
        if ($getShared) {
            return static::getSharedInstance('auth');
        }
        return new \App\Services\AuthService();
    }

    /**
     * Notification service - create and dismiss in-app notifications.
     */
    public static function notifications(bool $getShared = true): \App\Services\NotificationService
    {
        if ($getShared) {
            return static::getSharedInstance('notifications');
        }
        return new \App\Services\NotificationService();
    }

    /**
     * File uploader - centralised upload handler.
     */
    public static function fileUploader(bool $getShared = true): \App\Libraries\FileUploader
    {
        if ($getShared) {
            return static::getSharedInstance('fileUploader');
        }
        return new \App\Libraries\FileUploader();
    }
}
