<?php
auth(\_::$User->AdminAccess);
use MiMFa\Library\Convert;
use MiMFa\Library\Struct;
use MiMFa\Module\Table;
module("Table");
$module = new Table(table("Form_Inbox"));
$module->KeyColumns = ["User", "Form"];
$module->IncludeColumns = ["Id", "User" => "UserId", "Form" => "FormId", "UpdateTime"];
$module->ExcludeColumns = ["Id"];
$formId = get($data, "Form");
if ($formId) {
    $module->SelectCondition = "FormId=:Id";
    $module->SelectParameters = [":Id" => $formId];
}
$module->SelectCondition .= " ORDER BY UpdateTime DESC";
$module->AllowDataTranslation = false;
$module->Controlable =
    $module->Updatable = true;
$module->UpdateAccess = \_::$User->AdminAccess;
$forms = table("Form")->SelectPairs("Id", "Name");
$users = table("User")->SelectPairs("Id", "Name");
$module->CellsValues = [
    "User" => fn($v) => Struct::Span($forms[$v] ?? "", \_::$Address->UserRoot . $v),
    "Form" => fn($v) => Struct::Span($users[$v] ?? Struct::Icon('list-alt'), "/admin/content/forms/fields?Id=$v"),
    "CreateTime" => fn($v) => Convert::ToShownDateTimeString($v),
    "UpdateTime" => fn($v) => Convert::ToShownDateTimeString($v)
];
$module->CellsTypes = [
    "Id" => "number",
    "FormId" => "int",
    "UserId" => function ($t, $v) use ($users) {
        $std = new stdClass();
        $std->Title = "User";
        $std->Type = \_::$User->HasAccess(\_::$User->SuperAccess) ? "select" : "hidden";
        $std->Options = $users;
        if (!isValid($v))
            $std->Value = \_::$User->Id;
        return $std;
    },
    "Data" => "object",
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