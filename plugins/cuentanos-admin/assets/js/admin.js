/* ============================================
   CNMX Admin JavaScript
   ============================================ */

(function($) {
    'use strict';

    const CNMXAdmin = {
        init: function() {
            this.loadStats();
            this.bindEvents();
        },

        bindEvents: function() {
            $(document).on('click', '.cnmx-btn-approve', this.approveNegocio.bind(this));
            $(document).on('click', '.cnmx-btn-reject', this.rejectNegocio.bind(this));
            $(document).on('click', '.cnmx-btn-featured', this.toggleFeatured.bind(this));
        },

        loadStats: function() {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'cnmx_get_stats',
                    nonce: cnmxAdminData.nonce
                },
                success: function(data) {
                    $('#stat-negocios').text(data.total_negocios);
                    $('#stat-pendientes').text(data.pendientes);
                    $('#stat-usuarios').text(data.total_usuarios);
                    $('#stat-resenas').text(data.total_resenas);
                    $('#stat-megafonos').text(data.total_megafonos);
                    $('#pending-count').text(data.pendientes);

                    if (data.negocios_recientes && data.negocios_recientes.length) {
                        let html = '';
                        data.negocios_recientes.forEach(function(n) {
                            html += '<tr>';
                            html += '<td><strong>' + n.post_title + '</strong></td>';
                            html += '<td>' + n.post_date + '</td>';
                            html += '<td><a href="' + n.guid + '" target="_blank" class="button button-small">Ver</a></td>';
                            html += '</tr>';
                        });
                        $('#recent-businesses').html(html);
                    }
                }
            });
        },

        approveNegocio: function(e) {
            const $btn = $(e.currentTarget);
            const id = $btn.data('id');
            
            if (!confirm('¿Aprobar este negocio?')) return;

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'cnmx_approve_negocio',
                    nonce: cnmxAdminData.nonce,
                    negocio_id: id
                },
                success: function() {
                    $btn.closest('tr').fadeOut();
                    CNMXAdmin.loadStats();
                }
            });
        },

        rejectNegocio: function(e) {
            const $btn = $(e.currentTarget);
            const id = $btn.data('id');
            
            if (!confirm('¿Rechazar este negocio?')) return;

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'cnmx_reject_negocio',
                    nonce: cnmxAdminData.nonce,
                    negocio_id: id
                },
                success: function() {
                    $btn.closest('tr').fadeOut();
                }
            });
        },

        toggleFeatured: function(e) {
            const $btn = $(e.currentTarget);
            const id = $btn.data('id');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'cnmx_feature_negocio',
                    nonce: cnmxAdminData.nonce,
                    negocio_id: id,
                    featured: 'true'
                },
                success: function() {
                    $btn.text('Destacado ✓');
                    $btn.prop('disabled', true);
                }
            });
        }
    };

    $(document).ready(function() {
        CNMXAdmin.init();
    });

})(jQuery);
