CKEDITOR.editorConfig = function( config )
{
    config.extraPlugins = 'audio';
    config.language = 'ru';
    //config.skin = 'moono';

    // Add the button to toolbar
/*
    config.toolbar =
    [
        { name: 'document', items : [ 'Audio', 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },
        { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
        { name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
        { name: 'forms', items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 
            'HiddenField' ] },
        '/',
        { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
        { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv',
        '-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
        { name: 'links', items : [ 'Link','Unlink','Anchor' ] },
        { name: 'insert', items : [ 'Image','Flash','-','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
        '/',
        { name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
        { name: 'colors', items : [ 'TextColor','BGColor' ] },
        { name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','About' ] }
    ];
*/

    config.toolbar =
    [
        { name: 'styles', items : [ 'Styles', 'Format', 'Font', 'FontSize' ] },
        { name: 'basicstyles', items : [ 'RemoveFormat', '-', 'Bold','Italic','Underline','Strike','Subscript','Superscript' ] },
        { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv',
        '-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ] },
        '/',        
        { name: 'links', items : [ 'Link', 'Unlink','Anchor' ] },
        { name: 'insert', items : [ 'Image', 'Audio', 'Flash','-','Table','HorizontalRule','SpecialChar','Iframe' ] },
        { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
        { name: 'editing', items : [ 'Find','Replace','-','SelectAll' ] },
        { name: 'tools', items : [ 'Source', '-', 'Maximize', 'ShowBlocks','-','About' ] },
    ];
};

CKEDITOR.config.forcePasteAsPlainText = true;
CKEDITOR.config.pasteFromWordRemoveStyles = true;
CKEDITOR.config.pasteFromWordRemoveFontStyles = true;

// Block all custom styles in CKEditor
CKEDITOR.stylesSet.add( 'default', [
    //{ name : 'Картинка по центру', element : 'span', attributes : {'class':'text-center'} }
]);


// Задается в crud/form.twig
//CKEDITOR.config.contentsCss = '';

// get path of directory ckeditor
var basePath = CKEDITOR.basePath;
basePath = basePath.substr(0, basePath.indexOf("ckeditor/")); 
CKEDITOR.plugins.addExternal( 'audio', basePath + '../../ckeditor/plugins/audio/' );
