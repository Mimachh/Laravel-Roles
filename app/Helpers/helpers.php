<?php

function userName() {
    return auth()->user()->name;
}

function getRolesName() {
    $rolesName = "";

    $i = 0;

    foreach(auth()->user()->roles as $role) {
        $rolesName .= $role->name;

        if($i < sizeof(auth()->user()->roles) -1) {
            $rolesName .= ", ";
        }
        $i ++;
    }

    return $rolesName;
}