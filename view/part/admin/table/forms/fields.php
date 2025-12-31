<?php
auth(\_::$User->AdminAccess);
use MiMFa\Library\Convert;
use MiMFa\Library\Struct;
use MiMFa\Module\Table;
$types = [
    'Span' => "Inline text display (&lt;span&gt;), non-editable label.",
    'Division' => "Block container (&lt;div&gt;) for arbitrary content.",
    'Link' => "Anchor (&lt;a&gt;) that navigates to value or url.",
    'Action' => "Clickable element that triggers JS action or load a path.",
    'Icon' => "Clickable icon that triggers JS action or load a path.",
    'Button' => "Clickable button that triggers JS action or load a path.",
    'Paragraph' => "Paragraph (&lt;p&gt;) block of text.",
    'Label' => "Form label tied to an input (for attribute).",
    'Break' => "To go on the new line (&lt;br&gt;).",
    'BreakLine' => "A horizontal row (&lt;hr&gt;).",
    'Disabled' => "Read-only / disabled input presentation.",
    'Input' => "Generic single-line input (text by default).",
    'Collection' => "Collection of items (dl/list) or repeated inputs.",
    'Text' => "Single-line text input.",
    'Texts' => "Multi-line textarea input.",
    'Content' => "Rich content editor (textarea + live preview).",
    'Script' => "Code/script editor textarea (JS/HTML/CSS).",
    'Object' => "JSON/object editor (formatted textarea).",
    'Search' => "Search input (type=search).",
    'Find' => "Find/combo input with visible text + hidden value (datalist).",
    'Color' => "Color picker input (type=color).",
    'Dropdown' => "Single-select dropdown (&lt;select&gt;).",
    'Dropdowns' => "Multi-select dropdown (&lt;select multiple&gt;).",
    'Radio' => "Single radio input.",
    'Radios' => "Group of radio buttons (multiple choices).",
    'Switch' => "Boolean toggle (visual switch).",
    'Switches' => "Multiple boolean toggles.",
    'Check' => "Single checkbox input.",
    'Checks' => "Multiple checkboxes collection.",
    'Integer' => "Integer number input (min/max supported).",
    'Short' => "Small-range integer input (bounded short int).",
    'Long' => "Numeric input for larger integer values.",
    'Range' => "Slider input with min/max (range).",
    'Code' => "Numeric code input (digits, optional fixed length).",
    'Symbolic' => "Symbolic selector (visual symbols as choices).",
    'Float' => "Floating-point number input with precision/step.",
    'Tel' => "Telephone input (type=tel).",
    'Mask' => "Text input validated by regex pattern / mask.",
    'Url' => "URL input (validated, accepts absolute or root paths).",
    'Map' => "Map picker (interactive Leaflet map) producing lat,lng.",
    'Path' => "File-or-path input (text fallback + file chooser).",
    'Address' => "Multi-line address textarea.",
    'Calendar' => "Calendar widget with date/time selection + hidden value.",
    'Datetime' => "Datetime-local input control.",
    'Date' => "Date-only input control.",
    'Time' => "Time-only input control.",
    'Week' => "Week input control.",
    'Month' => "Month input control.",
    'Hidden' => "Hidden input field (type=hidden).",
    'Secret' => "Password input (type=password).",
    'Document' => "Single document file uploader (document formats).",
    'Documents' => "Multiple document uploader.",
    'Image' => "Single image file uploader (image formats).",
    'Images' => "Multiple image uploader.",
    'Audio' => "Single audio file uploader.",
    'Audios' => "Multiple audio uploader.",
    'Video' => "Single video file uploader.",
    'Videos' => "Multiple video uploader.",
    'File' => "Generic single file uploader.",
    'Files' => "Multiple file uploader.",
    'Directory' => "Directory selector (webkitdirectory / multiple).",
    'Email' => "Email input (type=email), validated format.",
    'Submit' => "Form submit button.",
    'Reset' => "Form reset button."
];
module("Table");
$module = new Table(table("Form_Field"));
$module->KeyColumns = ["Name", "Title"];
$module->IncludeColumns = ["Id", "Name", "Required", "Type", "Title", "Status", "Description", "Priority", "CreateTime"];
$module->ExcludeColumns = ["Id", "Required"];
$formId = get($data, "Form");
if ($formId) {
    $module->SelectCondition = "FormId=:Id";
    $module->SelectParameters = [":Id" => $formId];
}
$module->SelectCondition .= " ORDER BY Priority DESC";
$module->Controlable =
    $module->Updatable = true;
$module->UpdateAccess = \_::$User->AdminAccess;
$module->CellsValues = [
    "Name" => fn($v, $k, $r) => "\${{$v}}" . ($r["Required"] ? " " . Struct::Span("*", ["class" => "be fore red"]) : ""),
    "Status" => fn($v) => Struct::Span($v > 0 ? "Published" : ($v < 0 ? "Unpublished" : "Drafted")),
    "CreateTime" => fn($v) => Convert::ToShownDateTimeString($v),
    "UpdateTime" => fn($v) => Convert::ToShownDateTimeString($v)
];
$module->CellsTypes = [
    "Id" => "number",
    "FormId" => function ($t, $v) use ($formId) {
        $std = new stdClass();
        $std->Title = "Form";
        $std->Value = $v?:$formId;
        if($formId) $std->Type = "Hidden";
        else {
            $std->Type = "Select";
            $std->Options = table("Form")->SelectPairs();
        }
        return $std;
    },
    "Type" => function ($t, $v) use ($types) {
        $std = new stdClass();
        $std->Type = "Find";
        $std->Value = $v;
        $std->Options = $types;
        return $std;
    },
    "Name" => "string",
    "Title" => "string",
    "Value" => "string",
    "Description" => "strings",
    "Options" => "json",
    "Required" => "switch",
    "Priority" => "int",
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