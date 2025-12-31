<?php
(new Router())->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () {
        $id = getReceived("Id");
        $row = $id?table("Form")->Get($id):null;
        view("part", [
            "Form" => $id,
            "Name" => "admin/table/forms/fields",
            "Path" => "/form/$id",
            "Image" => ($row["Image"]??null)?:"list-alt",
            "Title" => "'".(($row["Title"]??null)?:"Form")."' 'Fields'",
            "Description" => ($row["Description"]??null)?:"'Edit' 'Form' 'Fields'"
        ]);
    })
    ->Default(function () {
        part("admin/table/forms/fields");
    })
    ->Handle();
?>