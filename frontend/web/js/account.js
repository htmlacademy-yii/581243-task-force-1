document.addEventListener("DOMContentLoaded", function() {
  Dropzone.autoDiscover = false;

  var dropzone = new Dropzone(".dropzone", {
    url: window.location.href, maxFiles: 6, uploadMultiple: true,
    acceptedFiles: 'image/*', previewTemplate: '<a href="#"><img data-dz-thumbnail alt="Фото работы"></a>',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }, maxFilesize: 2, parallelUploads: 6
  });
});
