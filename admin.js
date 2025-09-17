// Admin JavaScript pour Beef Chart Plugin
jQuery(document).ready(function($) {
    console.log('Beef Chart Admin JS loaded');
    
    // Fonction pour sauvegarder les données individuelles via AJAX
    function saveBeefData(id, price, available) {
        $.ajax({
            url: beefChartAjax.ajax_url,
            method: 'POST',
            data: {
                action: 'yfbcc_save_beef_data',
                id: id,
                price: price,
                available: available ? 1 : 0,
                nonce: beefChartAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Afficher un message de succès
                    console.log('Data saved successfully');
                } else {
                    console.error('Error saving data:', response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
            }
        });
    }
    
    // Gérer les changements de prix en temps réel
    $('.beef-chart-table input[type="number"]').on('change', function() {
        const row = $(this).closest('tr');
        const id = $(this).attr('name').match(/\[(\d+)\]/)[1];
        const price = parseFloat($(this).val());
        const available = row.find('input[type="checkbox"]').is(':checked');
        
        // Sauvegarder automatiquement après un court délai
        setTimeout(function() {
            saveBeefData(id, price, available);
        }, 500);
    });
    
    // Gérer les changements de disponibilité
    $('.beef-chart-table input[type="checkbox"]').on('change', function() {
        const row = $(this).closest('tr');
        const id = $(this).attr('name').match(/\[(\d+)\]/)[1];
        const price = parseFloat(row.find('input[type="number"]').val());
        const available = $(this).is(':checked');
        
        saveBeefData(id, price, available);
    });
});
