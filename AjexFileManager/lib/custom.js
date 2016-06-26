function makeFileList() {

	//get the input and UL list
	var input = document.getElementById('filesToUpload');

	//for every file...
	for (var x = 0; x < input.files.length; x++) {
		//add to list
		var newElem = '<div class="MultiFile-label">' +
						'<a class="MultiFile-remove" href="#filesToUpload_wrap">' +
							'<img src="skin/_ico/cross.png" alt="X">' +
						'</a> ' +
						'<span class="MultiFile-title">'+input.files[x].name+'</span>' +
					  '</div>';
		$('#uploadList').append(newElem);
	}
}