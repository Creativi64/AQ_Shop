Ext.namespace("Ext.ux.form");

if(froalaLoadOnFocusOnly === true)
{
    //console.log('froala onFocus version', froalaLoadOnFocusOnly);

    Ext.ux.form.FroalaEditor = Ext.extend(Ext.form.TextArea,  {

        initComponent: function(){},

        onFocus : function(ct, position)
        {
            froala_editorInit(this, ct, position);
            Ext.form.TextArea.superclass.onFocus.call(this, ct, position);
        }
    });
}
else {

    //console.log('froala afterRender version', froalaLoadOnFocusOnly);

    Ext.ux.form.FroalaEditor = Ext.extend(Ext.form.TextArea,  {

        initComponent: function(){},

        afterRender : function(ct, position)
        {
            froala_editorInit(this, ct, position);
            Ext.form.TextArea.superclass.afterRender.call(this, ct, position);
        }
    });
}

Ext.reg('froalaeditor', Ext.ux.form.FroalaEditor);


function froala_editorInit(sb, ct, position)
{
    if (!sb.el) {
        sb.defaultAutoCreate = {
            tag: "textarea",
            style: "width:100px;height:60px;",
            autocomplete: "off"
        }
    }

    const _id = '#'+sb.id;

    let xtClass = null;
    let textarea = null;
    let dom = ct.dom;
    if(!froalaLoadOnFocusOnly)
    {
        textarea = dom.childNodes[0];
    }
    else {
        textarea = sb.el.dom;
    }

    if(textarea)
    {
        textarea.classList.forEach(cls => {
            if(cls.startsWith("xtclass-"))
            {
                xtClass = cls.split('-')[1];
            }
        })
    }

    sb.editor = new FroalaEditor(
        _id,
        {
            language: froalaLanguage || 'en',

            iframe: true,

            imageUploadMethod: 'POST',
            imageUploadURL: '/xtAdmin/froala_upload_image.php',
            imageUploadParams: {
                currentType: 'image',
                xtClass: xtClass
            },
            imageManagerLoadURL: '/xtAdmin/froala_get_images.php?xtClass=' + xtClass,


            //videoAllowedProviders: ['youtube', 'vimeo'], // default is ['.*']
            //videoAllowedTypes: ['mp4', 'webm', 'ogg'],  // ist default
            videoDefaultAlign: 'left',
            videoDefaultDisplay: 'inline',
            videoResponsive: true,
            videoUploadMethod: 'POST',
            videoUploadURL: '/xtAdmin/froala_upload_video.php',
            videoUploadParams: {
                currentType: 'video',
                xtClass: xtClass
            },


            toolbarButtons: {
                'moreText': {
                    'buttons': ['bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', 'fontFamily', 'fontSize', 'textColor', 'backgroundColor', 'inlineClass', 'inlineStyle', 'clearFormatting']
                },
                'moreParagraph': {
                    'buttons': ['alignLeft', 'alignCenter', 'formatOLSimple', 'alignRight', 'alignJustify', 'formatOL', 'formatUL', 'paragraphFormat', 'paragraphStyle', 'lineHeight', 'outdent', 'indent', 'quote']
                },
                'moreRich': {
                    'buttons': ['insertLink', 'insertImage', 'insertVideo', 'insertTable', 'emoticons', 'fontAwesome', 'specialCharacters', 'embedly', 'insertFile', 'insertHR']
                },
                'moreMisc': {
                    'buttons': ['undo', 'redo', 'fullscreen', 'print', 'getPDF', 'spellChecker', 'selectAll', 'html', 'help']
                }
            },

            events: {
                'image.inserted_____': $img => {

                    // console.log('image.inserted', $img, this);
                    // bringt uns nix bei schon bestehenden ck bildern ?
                    // oder eben doch für die neu eingefügten
                },
                'html.get': html => {
                    //console.log('html.get', html, this);
                    let doUpdate = false;
                    let el = froala_createElementFromHTML(html);
                    el.querySelectorAll("img").forEach( img => {
                        let w = img.style.width.toString()
                        img.style.setProperty("width", w, "important");

                        if(!img.parentElement.classList.contains("fr-view"))
                        {
                            let div = document.createElement("div");
                            div.classList.add("fr-view");

                            img.parentNode.appendChild(div);

                            div.appendChild(img);

                            doUpdate = true;
                        }
                    });

                    // laut doku sollte der return wert übernommen werden, funzt nich
                    // daher das set, denke, dass ist nich so gut
                    // liegt dass daran, dass wir im ext sind ? und this nicht der editor ist
                    if(doUpdate)
                        sb.editor.html.set(el.innerHTML);

                    // return 'html.get test';
                }
            },
            key: "2J1B10dA5A5A5D4E4E3E3C-22VKOG1FGULVKHXDXNDXc2a1Kd1SNdF3H3A8A6D4F4D4E3C2A7==",
            attribution: false
        }
    );
}

function froala_createElementFromHTML(htmlString) {
    const div = document.createElement('div');
    div.innerHTML = htmlString.trim();

    // Change this to div.childNodes to support multiple top-level nodes.
    return div;
}

