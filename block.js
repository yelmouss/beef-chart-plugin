// Gutenberg Block pour Beef Chart
(function(blocks, element, editor) {
    var el = element.createElement;
    var registerBlockType = blocks.registerBlockType;
    var RichText = editor.RichText;

    registerBlockType('yfbcc/beef-chart', {
        title: 'French Beef Cuts Chart',
        icon: 'chart-bar',
        category: 'widgets',
        attributes: {
            width: {
                type: 'string',
                default: '100%'
            },
            height: {
                type: 'string',
                default: '500px'
            }
        },

        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return el('div', {
                className: 'beef-chart-block-editor',
                style: {
                    border: '1px solid #ddd',
                    padding: '20px',
                    textAlign: 'center',
                    backgroundColor: '#f9f9f9'
                }
            },
                el('h3', {}, 'Graphique des Coupes de Bœuf (FR)'),
                el('p', {}, 'Le graphique interactif des coupes de bœuf sera affiché ici.'),
                el('div', {
                    style: {
                        marginTop: '10px'
                    }
                },
                    el('label', {}, 'Largeur: '),
                    el('input', {
                        type: 'text',
                        value: attributes.width,
                        onChange: function(event) {
                            setAttributes({width: event.target.value});
                        },
                        style: {
                            margin: '0 10px'
                        }
                    }),
                    el('label', {}, 'Hauteur: '),
                    el('input', {
                        type: 'text',
                        value: attributes.height,
                        onChange: function(event) {
                            setAttributes({height: event.target.value});
                        },
                        style: {
                            margin: '0 10px'
                        }
                    })
                )
            );
        },

        save: function(props) {
            var attributes = props.attributes;
            
            return el('div', {
                className: 'beef-chart-shortcode'
            }, '[fbcc_beef_chart width="' + attributes.width + '" height="' + attributes.height + '"]');
        }
    });

})(
    window.wp.blocks,
    window.wp.element,
    window.wp.editor
);
