<?php

use App\Libraries\Auth;

if (!function_exists('auth')) {
    /**
     * Get Auth instance
     */
    function auth()
    {
        static $auth = null;
        if ($auth === null) {
            $auth = new Auth();
        }
        return $auth;
    }
}

if (!function_exists('isAdmin')) {
    /**
     * Check if user is admin
     */
    function isAdmin()
    {
        return auth()->isAdmin();
    }
}

if (!function_exists('hasRole')) {
    /**
     * Check if user has specific role
     */
    function hasRole($roleName)
    {
        return auth()->hasRole($roleName);
    }
}

if (!function_exists('hasRoles')) {
    /**
     * Check if user has any of the specified roles
     */
    function hasRoles($roleNames)
    {
        return auth()->hasRoles($roleNames);
    }
}

if (!function_exists('hasBuyer')) {
    /**
     * Check if user has specific buyer
     */
    function hasBuyer($buyerId)
    {
        return auth()->hasBuyer($buyerId);
    }
}

if (!function_exists('hasBuyers')) {
    /**
     * Check if user has any of the specified buyers
     */
    function hasBuyers($buyerIds)
    {
        return auth()->hasBuyers($buyerIds);
    }
}