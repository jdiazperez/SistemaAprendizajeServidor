<?php
/**
 * PHP version 7.2
 * src\Messages.php
 */

namespace TDW18\Usuarios;

class Messages
{
    const MESSAGES = [
        'tdw_unauthorized_401'
        => 'UNAUTHORIZED: invalid X-Token header',
        'tdw_pathnotfound_404'
        => 'Path not found',
        'tdw_notallowed_405'
        => 'Method not allowed',

        // login
        'tdw_post_login_404'
        => 'User not found or password does not match',

        // users
        'tdw_cget_users_403'
        => '`Forbidden` You don\'t have permission to access',
        'tdw_cget_users_404'
        => 'User object not found',
        'tdw_get_users_403'
        => '`Forbidden` You don\'t have permission to access',
        'tdw_get_users_404'
        => 'Resource not found',
        'tdw_delete_users_403'
        => '`Forbidden` You don\'t have permission to access',
        'tdw_delete_users_404'
        => 'Resource not found',
        'tdw_post_users_400'
        => '`Bad Request` Username or email already exists.',
        'tdw_post_users_403'
        => '`Forbidden` You don\'t have permission to access',
        'tdw_post_users_422'
        => '`Unprocessable entity` Username, e-mail or password is left out',
        'tdw_put_users_400'
        => '`Bad Request` User name or e-mail already exists',
        'tdw_put_users_403'
        => '`Forbidden` You don\'t have permission to access',
        'tdw_put_users_404'
        => 'Resource not found',
    ];
}
