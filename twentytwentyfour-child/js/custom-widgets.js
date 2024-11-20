( function( blocks, element, editor ) {
    var el = element.createElement;
    var RichText = editor.RichText;

    blocks.registerBlockType( 'custom/hello-world', {
        title: 'Hello World',
        category: 'common',
        attributes: {
            content: {
                type: 'string',
                source: 'html',
                selector: 'p',
            },
        },
        edit: function( props ) {
            function onChangeContent( newContent ) {
                props.setAttributes( { content: newContent } );
            }

            return el(
                RichText,
                {
                    tagName: 'p',
                    className: props.className,
                    onChange: onChangeContent,
                    value: props.attributes.content,
                }
            );
        },
        save: function( props ) {
            return el( RichText.Content, {
                tagName: 'p',
                value: props.attributes.content,
            } );
        },
    } );
} )( window.wp.blocks, window.wp.element, window.wp.editor );
