function Modal(path) {
    //Update image source in modal to source passed
    $("#modalImage").attr('src', path);

    //Make modal visible
    $("#modal").css("display", "block");
}

function closeModal() {
    //Set modal to invisible
    $("#modal").css("display", "none");
}
