// load modals
//
$(document).ready(function() {
	$('.modal').modal();
});

// like button
//
function like(id) {
	// send like to the backend
	apretaste.send({
		command: 'CITAS LIKE',
		redirect: false,
		data: {id: id}
	});

	// increase the likes conunter
	$('#likes').text($('#likes').text() * 1 + 1);

	// avoid multiple likes
	$('#likeBtn').attr('disabled', 'disabled').prop("onclick", null).off("click");

	// show message
	M.toast({html: '¡Me alegra que te guste!'});
}

// create a teaser to share
//
function teaser(text) {
	return text.length <= 50 ? text.trim() : text.trim().substr(0, 50).trim() + "...";
}

// share in Pizarra
//
function share() {
	// clean and shorten texts
	var quoteId = $('#quoteId').val();
	var message = $('#message').val();
	var title = teaser($('#shareModal .title').text());

	// share in pizarra
	apretaste.send({
		command: 'PIZARRA PUBLICAR',
		redirect: false,
		data: {
			text: message,
			image: '',
			link: {
				command: btoa(JSON.stringify({
					command: 'CITAS QUOTE',
					data: {id: quoteId}
				})),
				icon: 'newspaper',
				text: title
			}
		}
	});

	// show message
	M.toast({html: 'La cita fue compartida en Pizarra'});
}
