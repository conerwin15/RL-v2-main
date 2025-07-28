<?php

function toRoleLabel($role)
{
    if($role == 'dealer')
    {
        return \Lang::get('lang.dealer');
    } else if($role == 'staff') {
        return \Lang::get('lang.staff');
    } else {
        return $role;
    }
}
?>