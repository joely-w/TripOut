$(document).ready(function(e) {
	$("#uploadimage").on('submit', (function(e) {
		e.preventDefault();
		$.ajax({
			url: "/images/upload_image_process.php",
			type: "POST",
			data: new FormData(this),
			contentType: false,
			cache: false,
			processData: false,
			success: function(response) { /*Add image to div so user does not have to reload page and alert of success*/
				console.log(response);
				const jsonData = JSON.parse(response);
				if (jsonData.success === 1) {
					const content = `<div class="col-md-2 img-thumb"><a onclick="Modal('${jsonData.Filepath}')"><img src="${jsonData.Filepath}"></a></div>`;
					$("#images").append(content);
					$("#image-upload").append("<div class='alert alert-success' role='alert'> Image has been added!</div>");


				} else {
					$("#image-upload").append("<span class='alert alert-danger' role='alert'>" + jsonData.errors + "</span>");

				}
			},

		});
	}));
});

function fileHandler(arg) {
	var file = arg.files[0];
	var mime_types = ['image/jpeg', 'image/png'];
	if (mime_types.indexOf(file.type) == -1) {
		$("#image-upload").append("<div class='alert alert-error' role='alert'>That's not an image!</div>");
		return;
	}

	if (file.size > 10 * 1024 * 1024) {
		$("#image-upload").append("<div class='alert alert-error' role='alert'>File exceeds 10MB, try compressing!</div>");
		return;
	}
	var image_url = URL.createObjectURL(file);

	document.querySelector("#previewing").setAttribute('src', image_url);
}

function Modal(path) {
	document.getElementById("modalImage").src = path;
	document.getElementById("modal").style.display = "block";
}

function closeModal() {
	document.getElementById("modal").style.display = "none";

}