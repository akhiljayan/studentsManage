tinymce.init({
    mode: "exact",
    elements: "edit-note",
    plugins: "wordcount",
//    plugins: "wordcount textcolor table print insertdatetime colorpicker autoresize noneditable textpattern code bbcode",
    height: 100,
    max_chars: "10",
    max_chars_indicator: "lengthBox",
    relative_urls : false,
    remove_script_host : false,
    convert_urls : true,
//    toolbar: "forecolor backcolor insertdatetime undo redo styleselect bold italic link alignleft aligncenter alignright",
//    tools: "inserttable",
    insertdatetime_formats: ["%d.%m.%Y", "%H:%M"]
//    paste_data_images : true

});


