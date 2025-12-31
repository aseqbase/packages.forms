<?php
(new Router())->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () {
        view("part", [
            "Name" => "admin/table/forms",
            "Image" => "check-square",
            "Title" => "'Forms' 'Management'"
        ]);
    })
    ->Default(function () {
        part("admin/table/forms");
    })
    ->Handle();
?>