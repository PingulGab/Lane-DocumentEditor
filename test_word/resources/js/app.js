import $ from 'jquery';
import 'summernote/dist/summernote-lite';
import 'summernote/dist/summernote-lite.css';

// Initialize Summernote
$(document).ready(function() {
    $('#summernote').summernote({
        height: 300,   // Set the height of the editor
        placeholder: 'Edit your document here...',
    });
});
