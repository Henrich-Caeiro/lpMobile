/**
 * Acione Mobile CRM - Admin Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
    const ajaxUrl = window.acioneCrmAdmin?.ajax_url || '/wp-admin/admin-ajax.php';
    const nonce   = window.acioneCrmAdmin?.nonce || '';

    // Status Map para CSS classes
    const statusClassMap = {
        'Novo': 'status-novo',
        'Em Contato': 'status-em-contato',
        'Convertido': 'status-convertido',
        'Perdido': 'status-perdido',
    };

    const updateMetricsUI = (metrics) => {
        if (!metrics) return;
        const totalEl = document.getElementById('metric-total');
        const novosEl = document.getElementById('metric-novos');
        const contatoEl = document.getElementById('metric-contato');
        const convEl = document.getElementById('metric-convertidos');
        const taxaEl = document.getElementById('metric-conversao');

        if (totalEl) totalEl.textContent = metrics.total;
        if (novosEl) novosEl.textContent = metrics.novos;
        if (contatoEl) contatoEl.textContent = metrics.em_contato;
        if (convEl) convEl.textContent = metrics.convertidos;
        if (taxaEl) taxaEl.textContent = metrics.taxa_conversao + '%';

        // Atualiza badge de menu do WP se existir
        const menuBadge = document.querySelector('#toplevel_page_acione-crm .plugin-count');
        if (menuBadge) {
            menuBadge.textContent = metrics.novos;
            menuBadge.parentElement.style.display = metrics.novos > 0 ? '' : 'none';
        }
    };

    // 1. Atualização Assíncrona de Status
    document.querySelectorAll('.crm-status-select').forEach((select) => {
        let previousVal = select.value;

        select.addEventListener('change', (e) => {
            const target = e.target;
            const leadId = target.getAttribute('data-id');
            const newStatus = target.value;

            // Atualiza classe visual
            Object.values(statusClassMap).forEach((cls) => target.classList.remove(cls));
            if (statusClassMap[newStatus]) {
                target.classList.add(statusClassMap[newStatus]);
            }

            const formData = new FormData();
            formData.append('action', 'acione_crm_update_status');
            formData.append('nonce', nonce);
            formData.append('id', leadId);
            formData.append('status', newStatus);

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData,
            })
            .then((res) => res.json())
            .then((data) => {
                if (data && data.success) {
                    previousVal = newStatus;
                    updateMetricsUI(data.data?.metrics);
                } else {
                    alert(data?.data?.message || 'Falha ao atualizar status.');
                    target.value = previousVal;
                    Object.values(statusClassMap).forEach((cls) => target.classList.remove(cls));
                    if (statusClassMap[previousVal]) {
                        target.classList.add(statusClassMap[previousVal]);
                    }
                }
            })
            .catch((err) => {
                console.error('CRM Status Update Error:', err);
                alert('Erro de conexão ao salvar status.');
                target.value = previousVal;
            });
        });
    });

    // 2. Atualização Assíncrona de Notas Rápidas
    document.querySelectorAll('.crm-note-input').forEach((input) => {
        let timeout = null;
        let originalVal = input.value;
        const checkIcon = input.parentElement.querySelector('.crm-save-check');

        const saveNotes = () => {
            const currentVal = input.value;
            if (currentVal === originalVal) return;

            const leadId = input.getAttribute('data-id');
            const formData = new FormData();
            formData.append('action', 'acione_crm_update_notes');
            formData.append('nonce', nonce);
            formData.append('id', leadId);
            formData.append('notas', currentVal);

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData,
            })
            .then((res) => res.json())
            .then((data) => {
                if (data && data.success) {
                    originalVal = currentVal;
                    if (checkIcon) {
                        checkIcon.classList.add('active');
                        setTimeout(() => checkIcon.classList.remove('active'), 1800);
                    }
                }
            })
            .catch((err) => console.error('CRM Notes Error:', err));
        };

        input.addEventListener('blur', saveNotes);
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                input.blur();
            }
        });
    });

    // 3. Exclusão de Lead
    document.querySelectorAll('.crm-btn-delete').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const leadId = btn.getAttribute('data-id');
            if (!leadId) return;

            if (!confirm(`Deseja realmente remover o lead #${leadId}?`)) {
                return;
            }

            const row = document.getElementById(`lead-row-${leadId}`);
            if (row) {
                row.style.opacity = '0.3';
                row.style.pointerEvents = 'none';
            }

            const formData = new FormData();
            formData.append('action', 'acione_crm_delete_lead');
            formData.append('nonce', nonce);
            formData.append('id', leadId);

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData,
            })
            .then((res) => res.json())
            .then((data) => {
                if (data && data.success) {
                    if (row) {
                        row.remove();
                    }
                    updateMetricsUI(data.data?.metrics);
                } else {
                    alert(data?.data?.message || 'Erro ao remover lead.');
                    if (row) {
                        row.style.opacity = '';
                        row.style.pointerEvents = '';
                    }
                }
            })
            .catch((err) => {
                console.error('CRM Delete Error:', err);
                alert('Erro de conexão ao remover lead.');
                if (row) {
                    row.style.opacity = '';
                    row.style.pointerEvents = '';
                }
            });
        });
    });
});

