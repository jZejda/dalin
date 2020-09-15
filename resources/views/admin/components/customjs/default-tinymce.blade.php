<script src="{{ asset ("vendor/tinymce/tinymce.min.js") }}" type="text/javascript"></script>

<script>
//tinymce submit Please fill in this field
tinymce.init({
  selector: '#tinymcEditor',  // change this value according to your HTML
  language: 'cs',
  height: 300,
  entity_encoding : "raw",
  theme_advanced_blockformats : "dt,dd,code,samp",
  plugins: [
      'advlist autolink lists link image charmap print preview hr anchor pagebreak',
      'searchreplace wordcount visualblocks visualchars code fullscreen',
      'insertdatetime media nonbreaking save table contextmenu directionality',
      'emoticons template paste textcolor colorpicker textpattern imagetools noneditable'
        ],
   toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image fontawesome',

   content_css: ['https://netdna.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css', '/app/css/codepen.css'],
   noneditable_noneditable_class: 'fa',
   extended_valid_elements: 'span[*]'
});
</script>