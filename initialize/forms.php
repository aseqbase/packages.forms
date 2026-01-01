<?php

use MiMFa\Library\Contact;
use MiMFa\Library\Convert;
use MiMFa\Library\Local;
use MiMFa\Library\Struct;
use MiMFa\Module\Form;
if (isset(\_::$Front->MainMenus["Admin-Content"]["Items"])) {
    $menus = [
        "Forms" => [
            "Name" => "FORMS",
            "Path" => "/admin/content/forms",
            "Description" => "To manage all 'forms'",
            "Image" => "check-square"
        ]
    ];
    $menus["Access"] = \_::$User->AdminAccess;
    \_::$Front->MainMenus["Admin-Content"]["Items"] += $menus;
    \_::$Front->SideMenus["Admin-Content"]["Items"] += $menus;
}

\_::$Router->On("form")->Get(function () {
    $fm = table("Form")->SelectRow("*", ["Id=:Id OR Name=:Id", authCondition(checkAccess: false)], [":Id" => \_::$User->Page]);
    if (!$fm)
        route(404);
    else
        view(\_::$Front->DefaultViewName, [
            "WindowTitle" => $fm["Title"],
            "Content" => function () use ($fm) {
                module("Form");
                $form = new Form(
                    title: $fm["Title"],
                    image: $fm["Image"],
                    description: $fm["Description"],
                    method: $fm["Method"],
                    action: $fm["Action"]
                );
                $form->Template = $fm["Template"] ?: "d";
                $form->ResetLabel =
                    $form->CancelLabel =
                    $form->SubmitLabel = null;
                $form->Content = Struct::Convert($fm["Content"]);
                $form->Access = $fm["Access"];
                $form->ReceiverEmail = $fm["Email"];
                $form->Attributes = Convert::FromJson($fm["Attributes"]) ?: [];
                $buttons = [
                    'icon',
                    'button',
                    'submitbutton',
                    'submit',
                    'resetbutton',
                    'reset',
                    'imagesubmit',
                    'imgsubmit'
                ];
                $fields = table("Form_Field")->OrderBy("Priority DESC")->Select("*", ["FormId=:FId", authCondition()], [":FId" => $fm["Id"]]);
                $form->Buttons = loop(
                    filter($fields, fn($v) => in_array(strtolower($v["Type"]), $buttons)),
                    fn($v) => Struct::Field(
                        $v["Type"],
                        $v["Name"],
                        $v["Value"],
                        $v["Description"],
                        $v["Title"],
                        true,
                        Convert::FromJson($v["Options"]) ?: [],
                        attributes: [
                            ...($v["Required"] ? ["Required" => null] : []),
                            ...(Convert::FromJson($v["Attributes"]) ?: [])
                        ]
                    )
                );
                $form->Children =
                    [
                        Struct::HiddenInput("__FORM_ID", $fm["Id"]),
                        ...loop(
                            $fields,
                            fn($v) => Struct::Field(
                                $v["Type"],
                                $v["Name"],
                                $v["Value"],
                                $v["Description"],
                                $v["Title"],
                                true,
                                Convert::FromJson($v["Options"]) ?: [],
                                attributes: [
                                    ...($v["Required"] ? ["Required" => null] : []),
                                    ...(Convert::FromJson($v["Attributes"]) ?: [])
                                ]
                            )
                        )
                    ];

                if ($md = Convert::FromJson($fm["MetaData"]))
                    pod($form, $md);
                if ($s = $fm["Style"])
                    yield Struct::Style($s);
                yield $form->Handle();
                if ($s = $fm["Script"])
                    yield Struct::Script($s);
            }
        ]);
})->Default(
        function () {
            $r = receive();
            $id = pop($r, "__FORM_ID");
            if (!$id)
                deliverError("Does not received anythings!");
            else {
                $fm = table("Form")->SelectRow("*", ["Id=:Id", authCondition(checkAccess: false)], [":Id" => $id]);
                
                if($files = receiveFile()) {
                    foreach ($files as $key => $value) try{
                        $p = Local::Store($value);
                        $r[$key] = Local::GetAbsoluteUrl(Local::GetUrl($p));
                    }catch(\Exception $ex){}
                }
                table("Form_Inbox")->Insert(["FormId" => $id, "UserId" => \_::$User->Id, "Data"=>Convert::ToJson($r)]);
                if($fm["Email"]) Contact::SendHtmlEmail(\_::$User->Email??\_::$Front->SenderEmail, $fm["Email"], $fm["Title"]??$fm["Name"], Struct::Convert($r));
                deliverBreaker(Struct::Success("The 'form' submitted successfully!"), null, $fm["Target"]);
            }
        }
    );