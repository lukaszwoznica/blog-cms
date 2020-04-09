const toolbarOptions = [
    ['bold', 'italic', 'underline', 'strike'],
    ['blockquote', 'link'],
    [{'header': 1}, {'header': 2}],
    [{'list': 'ordered'}, {'list': 'bullet'}],
    [{'script': 'sub'}, {'script': 'super'}],
    [{'align': ''}, {'align': 'center'}, {'align': 'right'}, {'align': 'justify'}],
    ['clean']
];

const quill = new Quill('#editor-container', {
    modules: {
        toolbar: toolbarOptions
    },
    placeholder: 'Post content',
    theme: 'snow'
});