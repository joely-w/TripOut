let number_of_fields = 0;
let contentFields = []; /*Contains field keys, structure of each node: [{dataType,dataName}]*/
let toolbar_elements = [ /*Contains all element details for text sidebar, used to iteratively create toolbar instead of storing static html, structure: [FA Icon, Exec Command]*/
    ["underline", "underline"],
    ["italic", "italic"],
    ["bold", "bold"],
    ["scissors", "cut"],
    ["repeat", "redo"],
    ["strikethrough", "strikeThrough"],
    ["align-center", "justifyCenter"],
    ["align-left", "justifyLeft"],
    ["align-right", "justifyRight"]
];

function ImageAppender(array_index, data_value) {
    let image_array = contentFields[array_index].dataSrc;
    console.log(image_array);
    /* If image already in upload section, remove. Otherwise add image to sections selected images*/
    const index = image_array.indexOf(data_value);
    if (index > -1) {
        image_array.splice(index, 1);
    } else {
        image_array.push(data_value);
    }
}

function Add(content) {
    if (content === "image") {
        var container = document.createElement("div");
        container.className = "content-el";
        container.id = number_of_fields;
        container.className = "image-upload select-image row";
        $.ajax({
            url: "/images/myImages.php",
            type: 'GET',
            success: function (res) {
                const result = JSON.parse(res);
                for (let i = 0; i < result.length; i++) {
                    let imgcont = document.createElement("div");
                    imgcont.className = "cold-md-2 img-thumb";
                    imgcont.innerHTML = `<input type="checkbox" onchange="ImageAppender(number_of_fields-1, this.value)" value="${result[i][1]}" id="cont${number_of_fields}${i}"><label for="cont${number_of_fields}${i}"><img src="${result[i][0]}"></label>`;
                    container.appendChild(imgcont);
                }
            }
        });
        contentFields.push({dataType: 'image', dataSrc: []});
    }
    if (content === "text") {
        /*Create sidebar for editing text*/
        var container = document.createElement("div");
        container.className = "content-el";
        // noinspection JSValidateTypes
        container.id = number_of_fields;

        let sidebar = document.createElement("div");
        sidebar.className = "sidebar";
        let toolbar = document.createElement("div");
        toolbar.className = "toolbar";
        for (let i = 0; i < toolbar_elements.length; i++) {
            /*Loop through toolbar elements and create each button in toolbar*/
            const button = document.createElement("button");
            button.className = "btn btn-primary fa fa-" + toolbar_elements[i][0];
            const control = toolbar_elements[i][1];
            button.onclick = function () {
                document.execCommand(control, false, '')
            };
            button.type = "button";
            toolbar.appendChild(button);
        }
        sidebar.appendChild(toolbar);
        /*Create Rich Text Editor*/
        let Editor = document.createElement("div");
        Editor.contentEditable = "true";
        Editor.innerHTML = '<h1>Here\'s some content!</h1><p>Put some words here to talk about your event!</p>';
        Editor.className = "editor";
        Editor.id = `Text` + number_of_fields;
        /*Append Sidebar and Editor to DOM*/
        container.appendChild(Editor);
        container.appendChild(sidebar);
        contentFields.push({dataType: 'text', dataSrc: 'Text' + number_of_fields});
    }
    number_of_fields++;
    $("#usercontent").append(($(container)
            .hide()
            .fadeIn(500)
    ));
}

$("#event_form").submit(function (e) {
    e.preventDefault();
    processForm();
});

function processForm() {
    for (let i = 0; i < contentFields.length; i++) {
        if (contentFields[i].dataType === "text") { /*If text then grab text content and replace the id with the content*/
            contentFields[i].dataSrc = document.getElementById(contentFields[i].dataSrc).innerHTML; /*In processForm() as should not be called before user has finished editing and is submitting*/
        }
    }
    $.ajax({
        url: "/events/create_process.php",
        type: "POST",
        data: {eventTitle: document.getElementById("title").value, content: contentFields},
        success: function (response) {
            console.log(response);
            $('#create').innerHTML = '<h1>Form has been submitted!</h1>';
        },

    });
}