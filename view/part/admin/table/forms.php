<?php
auth(\_::$User->AdminAccess);
use MiMFa\Library\Convert;
use MiMFa\Library\Struct;
use MiMFa\Module\Table;
module("Table");
$module = new Table(table("Form"));
$module->KeyColumns = ["Name", "Title"];
$module->IncludeColumns = ["Id", "Name", "Image", "Title", "Status", "Inbox" => "Id", "Description", "UpdateTime"];
$module->ExcludeColumns = ["Id"];
$module->SelectCondition = "ORDER BY UpdateTime DESC";
$module->PrependControlsCreator = fn($v) => [
    Struct::Icon("inbox", "/admin/content/forms/inbox?Id=$v", ["tooltip" => "To see the 'inbox'"]),
    Struct::Icon("list-alt", "/admin/content/forms/fields?Id=$v", ["tooltip" => "To manage the 'fields'"]),
    Struct::Icon("arrow-up-right-from-square", "/form/$v", ["tooltip" => "To see the 'form'"])
];
$module->Controlable =
    $module->Updatable = true;
$module->UpdateAccess = \_::$User->AdminAccess;
$module->CellsValues = [
    "Name" => fn ($v, $k, $r)  => Struct::Link("\${{$v}}", "/admin/content/forms/fields?Id={$r["Id"]}", ["target" => "blank"]),
    "Title" => fn ($v, $k, $r)  => Struct::Link($v, "/form/{$r["Name"]}", ["target" => "blank"]),
    "Status" => fn($v) => Struct::Span($v > 0 ? "Published" : ($v < 0 ? "Unpublished" : "Drafted")),
    "Inbox" => fn($v) => Struct::Span(table("Form_Inbox")->Count("Id", "FormId=:Id", [":Id" => $v]), "/admin/content/forms/inbox?Id=$v"),
    "CreateTime" => fn($v) => Convert::ToShownDateTimeString($v),
    "UpdateTime" => fn($v) => Convert::ToShownDateTimeString($v)
];
$module->CellsTypes = [
    "Id" => "number",
    "Name" => "string",
    "Title" => "string",
    "Image" => "image",
    "Description" => "strings",
    "Email" => "email",
    "Method" => ["GET" => "GET", "POST" => "POST"],
    "Action" => "url",
    "Target" => "url",
    "Template" => [
        "d"=>"Default",
        'v'=>"Vertical",
        'h'=>"Horizontal",
        'b'=>"Both",
        't'=>"Table",
        's'=>"Special",
    ],
    "Content" => "content",
    "Status" => function ($t, $v) {
        $std = new stdClass();
        $std->Type = "Select";
        $std->Value = isEmpty($v)?1:$v;
        $std->Options = [-1 => "Unpublished", 0 => "Drafted", 1 => "Published"];
        return $std;
    },
    "Access" => function () {
        $std = new stdClass();
        $std->Type = "number";
        $std->Attributes = ["min" => \_::$User->BanAccess, "max" => \_::$User->SuperAccess];
        return $std;
    },
    "Attributes" => "json",
    "Style" => "CSS",
    "Script" => "JS",
    "UpdateTime" => function ($t, $v) {
        $std = new stdClass();
        $std->Type = \_::$User->HasAccess(\_::$User->SuperAccess) ? "calendar" : "hidden";
        $std->Value = Convert::ToDateTimeString();
        return $std;
    },
    "CreateTime" => function ($t, $v) {
        return \_::$User->HasAccess(\_::$User->SuperAccess) ? "calendar" : (isValid($v) ? "hidden" : false);
    },
    "MetaData" => "json"
];
pod($module, $data);
$module->Render();