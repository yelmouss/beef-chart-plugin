wp.blocks.registerBlockType('beef-chart/beef-chart', {
    title: 'Beef Chart',
    icon: 'chart-bar',
    category: 'widgets',
    attributes: {
        width: {
            type: 'string',
            default: '100%'
        },
        height: {
            type: 'string',
            default: '600px'
        }
    },
    edit: function(props) {
        return wp.element.createElement(
            'div',
            {
                style: {
                    border: '1px dashed #ccc',
                    padding: '20px',
                    textAlign: 'center',
                    background: '#f9f9f9'
                }
            },
            wp.element.createElement('h3', {}, 'Beef Chart Block'),
            wp.element.createElement('p', {}, 'Carte interactive des coupes de bœuf'),
            wp.element.createElement('p', { style: { fontSize: '12px', color: '#666' } }, 'Le graphique sera affiché sur la page publiée.')
        );
    },
    save: function(props) {
        return null; // Dynamic block
    }
});
