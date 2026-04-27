<?php

function can($permission_name)
{
    $permissions = session()->get('permissions') ?? [];

    return in_array($permission_name, $permissions);
}
