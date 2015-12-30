 /*
 * @file Audio plugin for CKEditor
 * This is only an adaptation of Video plugin for CKEditor created by Alfonso Martínez de Lizarrondo
 * Adapted by Philippe Jeoris
 *
 * == BEGIN LICENSE ==
 *
 * Licensed under the terms of any of the following licenses at your
 * choice:
 *
 *  - GNU General Public License Version 2 or later (the "GPL")
 *    http://www.gnu.org/licenses/gpl.html
 *
 *  - GNU Lesser General Public License Version 2.1 or later (the "LGPL")
 *    http://www.gnu.org/licenses/lgpl.html
 *
 *  - Mozilla Public License Version 1.1 or later (the "MPL")
 *    http://www.mozilla.org/MPL/MPL-1.1.html
 *
 * == END LICENSE ==
 *
 */

( function() {

CKEDITOR.plugins.add( 'audio',
{
	// Translations, available at the end of this file, without extra requests
	lang : [ 'en', 'ru' ],

	getPlaceholderCss : function()
	{
		return 'img.cke_audio' +
				'{' +
					'background-image: url(' + CKEDITOR.getUrl( this.path + 'images/placeholder.png' ) + ');' +
					'background-position: center center;' +
					'background-repeat: no-repeat;' +
					'background-color:gray;'+
					'border: 1px solid #a9a9a9;' +
					'width: 160px;' +
					'height: 40px;' +
				'}';
	},

	onLoad : function()
	{
		// v4
		if (CKEDITOR.addCss)
			CKEDITOR.addCss( this.getPlaceholderCss() );

	},

	init : function( editor )
	{
		var lang = editor.lang.audio;

		// Check for CKEditor 3.5
		if (typeof editor.element.data == 'undefined')
		{
			alert('The "audio" plugin requires CKEditor 3.5 or newer');
			return;
		}

		CKEDITOR.dialog.add( 'audio', this.path + 'dialogs/audio.js' );

		editor.addCommand( 'Audio', new CKEDITOR.dialogCommand( 'audio' ) );
		editor.ui.addButton( 'Audio',
			{
				label : lang.toolbar,
				command : 'Audio',
				icon : this.path + 'images/icon.png'
			} );

		// v3
		if (editor.addCss)
			editor.addCss( this.getPlaceholderCss() );


		// If the "menu" plugin is loaded, register the menu items.
		if ( editor.addMenuItems )
		{
			editor.addMenuItems(
				{
					audio :
					{
						label : lang.properties,
						command : 'Audio',
						group : 'flash'
					}
				});
		}

		editor.on( 'doubleclick', function( evt )
			{
				var element = evt.data.element;

				if ( element.is( 'img' ) && element.data( 'cke-real-element-type' ) == 'audio' )
					evt.data.dialog = 'audio';
			});

		// If the "contextmenu" plugin is loaded, register the listeners.
		if ( editor.contextMenu )
		{
			editor.contextMenu.addListener( function( element, selection )
				{
					if ( element && element.is( 'img' ) && !element.isReadOnly()
							&& element.data( 'cke-real-element-type' ) == 'audio' )
						return { audio : CKEDITOR.TRISTATE_OFF };
				});
		}

		// Add special handling for these items
		CKEDITOR.dtd.$empty['cke:source']=1;
		CKEDITOR.dtd.$empty['source']=1;

		editor.lang.fakeobjects.audio = lang.fakeObject;


	}, //Init

	afterInit: function( editor )
	{
		var dataProcessor = editor.dataProcessor,
			htmlFilter = dataProcessor && dataProcessor.htmlFilter,
			dataFilter = dataProcessor && dataProcessor.dataFilter;

		// dataFilter : conversion from html input to internal data
		dataFilter.addRules(
			{

			elements : {
				$ : function( realElement )
				{
						if ( realElement.name == 'audio' )
						{
							realElement.name = 'cke:audio';
							for( var i=0; i < realElement.children.length; i++)
							{
								if ( realElement.children[ i ].name == 'source' )
									realElement.children[ i ].name = 'cke:source'
							}

							var fakeElement = editor.createFakeParserElement( realElement, 'cke_audio', 'audio', false ),
								fakeStyle = fakeElement.attributes.style || '';
							
							return fakeElement;
						}
				}
			}

			}
		);

	} // afterInit

} ); // plugins.add


var en = {
		toolbar	: 'Audio',
		dialogTitle : 'Audio properties',
		fakeObject : 'Audio',
		properties : 'Edit audio',
		sourceAudio: 'Source audio',
		sourceAudioMp3: 'Source audio "mp3" (IE, Chrome, Safari)',
		sourceAudioOgg: 'Source audio "ogg" (Opera, Firefox)',
		sourceType : 'Audio type',
		linkTemplate :  '<a href="%src%">%type%</a> ',
		fallbackTemplate : 'Your browser doesn\'t support audio.<br>Please download the file: %links%'
	};

var ru = {
		toolbar	: 'Аудио',
		dialogTitle : 'Свойства аудио',
		fakeObject : 'Audio',
		properties : 'Редактировать аудио',
		sourceAudio: 'Путь до файла',
		sourceAudioMp3: 'Путь до файла "mp3" (IE, Chrome, Safari)',
		sourceAudioOgg: 'Путь до файла "ogg" (Opera, Firefox)',
		sourceType : 'Тип файла',
		linkTemplate :  '<a href="%src%">%type%</a> ',
		fallbackTemplate : 'Ваш браузер не поддерживает аудио:<br>Вы можете загрузить файл: %links%'
	};

	// v3
	if (CKEDITOR.skins)
	{
		en = { audio : en} ;
		ru = { audio : ru} ;
	}

// Translations
CKEDITOR.plugins.setLang( 'audio', 'en', en );
CKEDITOR.plugins.setLang( 'audio', 'ru', ru );

})();