<?php
(new Router())->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () {
        $id = getReceived("Id");
        $row = $id?table("Form")->Get($id):null;
        view("part", [
            "Form" => $id,
            "Name" => "admin/table/forms/inbox",
            "Path" => "/form/$id",
            "Image" => ($row["Image"]??null)?:"inbox",
            "Title" => "'".(($row["Title"]??null)?:"Form")."' 'Inbox'",
            "Description" => ($row["Description"]??null)?:"'Manage' 'Form' 'Inbox'"
        ]);
    })
    ->Default(function () {
        part("admin/table/forms/inbox");
    })
    ->Handle();
?>