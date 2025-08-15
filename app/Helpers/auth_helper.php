<?php

if (!function_exists('hasRole')) {
    /**
     * Check if user has specific role
     */
    function hasRole($roleName)
    {
        if (!session()->get('logged_in')) {
            return false;
        }

        $userRoles = session()->get('role');
        if (is_string($userRoles)) {
            $roleArray = explode(', ', $userRoles);
            return in_array($roleName, $roleArray);
        }

        return false;
    }
}

if (!function_exists('hasRoleId')) {
    /**
     * Check if user has specific role ID
     */
    function hasRoleId($roleId)
    {
        if (!session()->get('logged_in')) {
            return false;
        }

        $roleIds = session()->get('role_ids');
        return is_array($roleIds) && in_array($roleId, $roleIds);
    }
}

if (!function_exists('isAdmin')) {
    /**
     * Check if user is admin
     */
    function isAdmin()
    {
        return hasRole('Admin');
    }
}

if (!function_exists('checkPageAccess')) {
    /**
     * Check page access and redirect if unauthorized
     */
    function checkPageAccess($requiredRole)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/');
        }

        if (!hasRole($requiredRole)) {
            return redirect()->to('/home')->with('error', 'Access denied. You do not have permission to access this page.');
        }

        return true;
    }
}