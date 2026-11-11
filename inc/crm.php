<?php
/**
 * CRM Nativo Acione Mobile
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

const ACIONE_CRM_DB_VER = '1.0.0';

/**
 * Criação da tabela de leads via dbDelta
 */
function acione_crm_install_table() {
    global $wpdb;
    $table_name      = $wpdb->prefix . 'acione_crm_leads';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        tipo_usuario varchar(100) NOT NULL DEFAULT '',
        nome_empresa varchar(255) NOT NULL DEFAULT '',
        telefone_whatsapp varchar(50) NOT NULL DEFAULT '',
        localizacao_rodovia varchar(255) NOT NULL DEFAULT '',
        notas text NOT NULL,
        status_lead varchar(50) NOT NULL DEFAULT 'Novo',
        data_hora datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY status_lead (status_lead),
        KEY data_hora (data_hora)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );

    update_option( 'acione_crm_db_version', ACIONE_CRM_DB_VER );
}
add_action( 'after_switch_theme', 'acione_crm_install_table' );

add_action( 'admin_init', function() {
    if ( get_option( 'acione_crm_db_version' ) !== ACIONE_CRM_DB_VER ) {
        acione_crm_install_table();
    }
} );

/**
 * Menu Administrativo
 */
function acione_crm_register_admin_menu() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'acione_crm_leads';
    $novos = 0;
    if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) === $table_name ) {
        $novos = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE status_lead = 'Novo'" );
    }

    $menu_title = 'CRM Acione';
    if ( $novos > 0 ) {
        $menu_title .= sprintf( ' <span class="update-plugins count-%1$d"><span class="plugin-count">%1$d</span></span>', $novos );
    }

    add_menu_page(
        'CRM Acione Mobile',
        $menu_title,
        'manage_options',
        'acione-crm',
        'acione_crm_render_admin_page',
        'dashicons-chart-pie',
        26
    );
}
add_action( 'admin_menu', 'acione_crm_register_admin_menu' );

/**
 * Enqueue scripts e estilos no Admin e Frontend
 */
function acione_crm_admin_enqueue( $hook ) {
    if ( $hook !== 'toplevel_page_acione-crm' ) {
        return;
    }

    $css_path = get_template_directory() . '/assets/css/admin-crm.css';
    $js_path  = get_template_directory() . '/assets/js/admin-crm.js';

    $css_ver = file_exists( $css_path ) ? filemtime( $css_path ) : ACIONE_CRM_DB_VER;
    $js_ver  = file_exists( $js_path ) ? filemtime( $js_path ) : ACIONE_CRM_DB_VER;

    wp_enqueue_style(
        'acione-admin-crm',
        get_template_directory_uri() . '/assets/css/admin-crm.css',
        array(),
        $css_ver
    );

    wp_enqueue_script(
        'acione-admin-crm',
        get_template_directory_uri() . '/assets/js/admin-crm.js',
        array(),
        $js_ver,
        true
    );

    wp_localize_script( 'acione-admin-crm', 'acioneCrmAdmin', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'acione_crm_admin_action' ),
    ) );
}
add_action( 'admin_enqueue_scripts', 'acione_crm_admin_enqueue' );

function acione_crm_frontend_localize() {
    wp_localize_script( 'acione-interactive', 'acioneCrmFrontend', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'acione_crm_lead_save' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'acione_crm_frontend_localize', 20 );

/**
 * AJAX Frontend: Salvar Lead do "demo-request-form"
 */
function acione_crm_handle_frontend_lead() {
    check_ajax_referer( 'acione_crm_lead_save', 'nonce' );

    $tipo_usuario        = sanitize_text_field( wp_unslash( $_POST['tipo_usuario'] ?? '' ) );
    $nome_empresa        = sanitize_text_field( wp_unslash( $_POST['nome_empresa'] ?? '' ) );
    $telefone_whatsapp   = sanitize_text_field( wp_unslash( $_POST['telefone_whatsapp'] ?? '' ) );
    $localizacao_rodovia = sanitize_text_field( wp_unslash( $_POST['localizacao_rodovia'] ?? '' ) );

    if ( empty( $nome_empresa ) || empty( $telefone_whatsapp ) ) {
        wp_send_json_error( array( 'message' => 'Campos obrigatórios não preenchidos.' ), 400 );
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'acione_crm_leads';

    $inserted = $wpdb->insert(
        $table_name,
        array(
            'tipo_usuario'        => $tipo_usuario,
            'nome_empresa'        => $nome_empresa,
            'telefone_whatsapp'   => $telefone_whatsapp,
            'localizacao_rodovia' => $localizacao_rodovia,
            'notas'               => '',
            'status_lead'         => 'Novo',
            'data_hora'           => current_time( 'mysql' ),
        ),
        array( '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
    );

    if ( false === $inserted ) {
        wp_send_json_error( array( 'message' => 'Erro interno ao gravar dados.' ), 500 );
    }

    wp_send_json_success( array(
        'message' => 'Lead registrado com sucesso.',
        'lead_id' => $wpdb->insert_id,
    ) );
}
add_action( 'wp_ajax_acione_save_lead', 'acione_crm_handle_frontend_lead' );
add_action( 'wp_ajax_nopriv_acione_save_lead', 'acione_crm_handle_frontend_lead' );

/**
 * AJAX Admin: Atualizar Status do Lead
 */
function acione_crm_ajax_update_status() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Sem permissão.' ), 403 );
    }
    check_ajax_referer( 'acione_crm_admin_action', 'nonce' );

    $lead_id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
    $status  = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '';

    $allowed = array( 'Novo', 'Em Contato', 'Convertido', 'Perdido' );
    if ( ! in_array( $status, $allowed, true ) || ! $lead_id ) {
        wp_send_json_error( array( 'message' => 'Parâmetros inválidos.' ), 400 );
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'acione_crm_leads';

    $updated = $wpdb->update(
        $table_name,
        array( 'status_lead' => $status ),
        array( 'id' => $lead_id ),
        array( '%s' ),
        array( '%d' )
    );

    if ( false === $updated ) {
        wp_send_json_error( array( 'message' => 'Falha ao atualizar status.' ), 500 );
    }

    $metrics = acione_crm_get_metrics();
    wp_send_json_success( array(
        'message' => 'Status atualizado com sucesso.',
        'metrics' => $metrics,
    ) );
}
add_action( 'wp_ajax_acione_crm_update_status', 'acione_crm_ajax_update_status' );

/**
 * AJAX Admin: Atualizar Notas Rápidas
 */
function acione_crm_ajax_update_notes() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Sem permissão.' ), 403 );
    }
    check_ajax_referer( 'acione_crm_admin_action', 'nonce' );

    $lead_id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
    $notas   = isset( $_POST['notas'] ) ? sanitize_textarea_field( wp_unslash( $_POST['notas'] ) ) : '';

    if ( ! $lead_id ) {
        wp_send_json_error( array( 'message' => 'Lead ID inválido.' ), 400 );
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'acione_crm_leads';

    $updated = $wpdb->update(
        $table_name,
        array( 'notas' => $notas ),
        array( 'id' => $lead_id ),
        array( '%s' ),
        array( '%d' )
    );

    if ( false === $updated ) {
        wp_send_json_error( array( 'message' => 'Falha ao gravar notas.' ), 500 );
    }

    wp_send_json_success( array( 'message' => 'Notas salvas.' ) );
}
add_action( 'wp_ajax_acione_crm_update_notes', 'acione_crm_ajax_update_notes' );

/**
 * AJAX Admin: Excluir Lead
 */
function acione_crm_ajax_delete_lead() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Sem permissão.' ), 403 );
    }
    check_ajax_referer( 'acione_crm_admin_action', 'nonce' );

    $lead_id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
    if ( ! $lead_id ) {
        wp_send_json_error( array( 'message' => 'ID inválido.' ), 400 );
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'acione_crm_leads';

    $deleted = $wpdb->delete(
        $table_name,
        array( 'id' => $lead_id ),
        array( '%d' )
    );

    if ( ! $deleted ) {
        wp_send_json_error( array( 'message' => 'Erro ao remover lead.' ), 500 );
    }

    $metrics = acione_crm_get_metrics();
    wp_send_json_success( array(
        'message' => 'Lead excluído.',
        'metrics' => $metrics,
    ) );
}
add_action( 'wp_ajax_acione_crm_delete_lead', 'acione_crm_ajax_delete_lead' );

/**
 * Retorna contadores de métricas
 */
function acione_crm_get_metrics() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'acione_crm_leads';

    $total        = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
    $novos        = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE status_lead = 'Novo'" );
    $em_contato   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE status_lead = 'Em Contato'" );
    $convertidos  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE status_lead = 'Convertido'" );
    $perdidos     = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE status_lead = 'Perdido'" );

    $taxa_conversao = $total > 0 ? round( ( $convertidos / $total ) * 100, 1 ) : 0;

    return compact( 'total', 'novos', 'em_contato', 'convertidos', 'perdidos', 'taxa_conversao' );
}

/**
 * Exportação para CSV nativa
 */
function acione_crm_export_csv() {
    if ( ! isset( $_GET['page'], $_GET['action'] ) || $_GET['page'] !== 'acione-crm' || $_GET['action'] !== 'export_csv' ) {
        return;
    }

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Acesso negado.' );
    }

    check_admin_referer( 'acione_crm_export_csv' );

    global $wpdb;
    $table_name = $wpdb->prefix . 'acione_crm_leads';

    $status = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';
    $tipo   = isset( $_GET['tipo'] ) ? sanitize_text_field( wp_unslash( $_GET['tipo'] ) ) : '';
    $search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

    $where  = array( '1=1' );
    $params = array();

    if ( ! empty( $status ) ) {
        $where[]  = 'status_lead = %s';
        $params[] = $status;
    }
    if ( ! empty( $tipo ) ) {
        $where[]  = 'tipo_usuario = %s';
        $params[] = $tipo;
    }
    if ( ! empty( $search ) ) {
        $like     = '%' . $wpdb->esc_like( $search ) . '%';
        $where[]  = '(nome_empresa LIKE %s OR telefone_whatsapp LIKE %s OR localizacao_rodovia LIKE %s OR notas LIKE %s)';
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    $where_sql = implode( ' AND ', $where );
    $query     = "SELECT * FROM $table_name WHERE $where_sql ORDER BY id DESC";

    if ( ! empty( $params ) ) {
        $leads = $wpdb->get_results( $wpdb->prepare( $query, $params ) );
    } else {
        $leads = $wpdb->get_results( $query );
    }

    $filename = 'acione-mobile-leads-' . gmdate( 'Y-m-d-His' ) . '.csv';

    header( 'Content-Type: text/csv; charset=UTF-8' );
    header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
    header( 'Pragma: no-cache' );
    header( 'Expires: 0' );

    $output = fopen( 'php://output', 'w' );
    // BOM UTF-8 para Excel
    fprintf( $output, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );

    fputcsv( $output, array(
        'ID',
        'Data/Hora',
        'Tipo de Usuario',
        'Nome / Empresa',
        'Telefone WhatsApp',
        'Localizacao / Rodovia',
        'Status',
        'Notas',
    ), ';' );

    foreach ( $leads as $lead ) {
        fputcsv( $output, array(
            $lead->id,
            $lead->data_hora,
            $lead->tipo_usuario,
            $lead->nome_empresa,
            $lead->telefone_whatsapp,
            $lead->localizacao_rodovia,
            $lead->status_lead,
            $lead->notas,
        ), ';' );
    }

    fclose( $output );
    exit;
}
add_action( 'admin_init', 'acione_crm_export_csv' );

/**
 * Renderização da Página do Dashboard CRM
 */
function acione_crm_render_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'acione_crm_leads';

    $metrics = acione_crm_get_metrics();

    $current_page = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
    $per_page     = 15;
    $offset       = ( $current_page - 1 ) * $per_page;

    $filter_status = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';
    $filter_tipo   = isset( $_GET['tipo'] ) ? sanitize_text_field( wp_unslash( $_GET['tipo'] ) ) : '';
    $search        = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

    $where  = array( '1=1' );
    $params = array();

    if ( ! empty( $filter_status ) ) {
        $where[]  = 'status_lead = %s';
        $params[] = $filter_status;
    }
    if ( ! empty( $filter_tipo ) ) {
        $where[]  = 'tipo_usuario = %s';
        $params[] = $filter_tipo;
    }
    if ( ! empty( $search ) ) {
        $like     = '%' . $wpdb->esc_like( $search ) . '%';
        $where[]  = '(nome_empresa LIKE %s OR telefone_whatsapp LIKE %s OR localizacao_rodovia LIKE %s OR notas LIKE %s)';
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    $where_sql = implode( ' AND ', $where );

    if ( ! empty( $params ) ) {
        $total_leads_query = $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE $where_sql", $params );
        $total_filtered    = (int) $wpdb->get_var( $total_leads_query );

        $query_params   = array_merge( $params, array( $per_page, $offset ) );
        $leads_query    = $wpdb->prepare( "SELECT * FROM $table_name WHERE $where_sql ORDER BY id DESC LIMIT %d OFFSET %d", $query_params );
        $leads          = $wpdb->get_results( $leads_query );
    } else {
        $total_filtered = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE $where_sql" );
        $leads          = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE $where_sql ORDER BY id DESC LIMIT %d OFFSET %d", $per_page, $offset ) );
    }

    $total_pages = ceil( $total_filtered / $per_page );

    $export_nonce = wp_create_nonce( 'acione_crm_export_csv' );
    $export_url   = add_query_arg( array(
        'page'     => 'acione-crm',
        'action'   => 'export_csv',
        '_wpnonce' => $export_nonce,
        'status'   => $filter_status,
        'tipo'     => $filter_tipo,
        's'        => $search,
    ), admin_url( 'admin.php' ) );
    ?>
    <div class="wrap acione-crm-wrap" id="acione-crm-app">
        <!-- Header -->
        <header class="crm-top-bar">
            <div class="crm-title-group">
                <div class="crm-badge">PRO</div>
                <h1>CRM Acione Mobile</h1>
                <span class="crm-subtitle">Gestão em tempo real de contatos e socorros</span>
            </div>
            <div class="crm-top-actions">
                <a href="<?php echo esc_url( $export_url ); ?>" class="crm-btn crm-btn-secondary" title="Exportar dados filtrados">
                    <span class="dashicons dashicons-download"></span> Exportar CSV
                </a>
            </div>
        </header>

        <!-- Metric Cards -->
        <section class="crm-metrics-grid">
            <div class="crm-metric-card" data-metric="total">
                <div class="metric-icon"><span class="dashicons dashicons-groups"></span></div>
                <div class="metric-info">
                    <span class="metric-label">Total de Leads</span>
                    <strong class="metric-val" id="metric-total"><?php echo esc_html( $metrics['total'] ); ?></strong>
                </div>
            </div>

            <div class="crm-metric-card highlight-orange" data-metric="novos">
                <div class="metric-icon"><span class="dashicons dashicons-warning"></span></div>
                <div class="metric-info">
                    <span class="metric-label">Novos Leads</span>
                    <strong class="metric-val" id="metric-novos"><?php echo esc_html( $metrics['novos'] ); ?></strong>
                </div>
            </div>

            <div class="crm-metric-card" data-metric="em_contato">
                <div class="metric-icon"><span class="dashicons dashicons-phone"></span></div>
                <div class="metric-info">
                    <span class="metric-label">Em Contato</span>
                    <strong class="metric-val" id="metric-contato"><?php echo esc_html( $metrics['em_contato'] ); ?></strong>
                </div>
            </div>

            <div class="crm-metric-card highlight-green" data-metric="convertidos">
                <div class="metric-icon"><span class="dashicons dashicons-yes-alt"></span></div>
                <div class="metric-info">
                    <span class="metric-label">Convertidos</span>
                    <strong class="metric-val" id="metric-convertidos"><?php echo esc_html( $metrics['convertidos'] ); ?></strong>
                </div>
            </div>

            <div class="crm-metric-card" data-metric="conversao">
                <div class="metric-icon"><span class="dashicons dashicons-chart-line"></span></div>
                <div class="metric-info">
                    <span class="metric-label">Conversão</span>
                    <strong class="metric-val" id="metric-conversao"><?php echo esc_html( $metrics['taxa_conversao'] ); ?>%</strong>
                </div>
            </div>
        </section>

        <!-- Filter Bar -->
        <section class="crm-filter-section">
            <form method="GET" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" class="crm-filter-form">
                <input type="hidden" name="page" value="acione-crm">

                <div class="crm-filter-inputs">
                    <div class="crm-search-box">
                        <span class="dashicons dashicons-search"></span>
                        <input type="text" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="Buscar nome, empresa, fone, rodovia...">
                    </div>

                    <select name="status" class="crm-select">
                        <option value="">Status: Todos</option>
                        <option value="Novo" <?php selected( $filter_status, 'Novo' ); ?>>Novo</option>
                        <option value="Em Contato" <?php selected( $filter_status, 'Em Contato' ); ?>>Em Contato</option>
                        <option value="Convertido" <?php selected( $filter_status, 'Convertido' ); ?>>Convertido</option>
                        <option value="Perdido" <?php selected( $filter_status, 'Perdido' ); ?>>Perdido</option>
                    </select>

                    <select name="tipo" class="crm-select">
                        <option value="">Tipo: Todos</option>
                        <option value="transportadora" <?php selected( $filter_tipo, 'transportadora' ); ?>>Transportadora</option>
                        <option value="motorista" <?php selected( $filter_tipo, 'motorista' ); ?>>Motorista</option>
                        <option value="prestador" <?php selected( $filter_tipo, 'prestador' ); ?>>Prestador de Serviço</option>
                    </select>

                    <button type="submit" class="crm-btn crm-btn-primary">Filtrar</button>
                    <?php if ( ! empty( $search ) || ! empty( $filter_status ) || ! empty( $filter_tipo ) ) : ?>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=acione-crm' ) ); ?>" class="crm-btn crm-btn-outline">Limpar</a>
                    <?php endif; ?>
                </div>

                <div class="crm-filter-count">
                    <span><?php echo esc_html( $total_filtered ); ?></span> registros encontrados
                </div>
            </form>
        </section>

        <!-- Leads Table -->
        <section class="crm-table-container">
            <table class="crm-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th style="width: 140px;">Data/Hora</th>
                        <th style="width: 130px;">Tipo</th>
                        <th>Nome / Empresa</th>
                        <th style="width: 160px;">WhatsApp</th>
                        <th>Local / Rodovia</th>
                        <th style="width: 140px;">Status</th>
                        <th>Notas Rápidas</th>
                        <th style="width: 60px; text-align: center;">Ações</th>
                    </tr>
                </thead>
                <tbody id="crm-table-body">
                    <?php if ( empty( $leads ) ) : ?>
                        <tr>
                            <td colspan="9" class="crm-empty-state">
                                <span class="dashicons dashicons-admin-post"></span>
                                <p>Nenhum lead encontrado.</p>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ( $leads as $lead ) :
                            $clean_phone = preg_replace( '/\D/', '', $lead->telefone_whatsapp );
                            if ( strlen( $clean_phone ) === 10 || strlen( $clean_phone ) === 11 ) {
                                $wa_url = 'https://wa.me/55' . $clean_phone;
                            } else {
                                $wa_url = 'https://wa.me/' . $clean_phone;
                            }
                            $status_slug = sanitize_title( $lead->status_lead );
                        ?>
                            <tr id="lead-row-<?php echo esc_attr( $lead->id ); ?>" data-lead-id="<?php echo esc_attr( $lead->id ); ?>">
                                <td class="col-id">#<?php echo esc_html( $lead->id ); ?></td>
                                <td class="col-date"><?php echo esc_html( gmdate( 'd/m/y H:i', strtotime( $lead->data_hora ) ) ); ?></td>
                                <td class="col-type">
                                    <span class="crm-badge-type type-<?php echo esc_attr( sanitize_title( $lead->tipo_usuario ) ); ?>">
                                        <?php echo esc_html( ucfirst( $lead->tipo_usuario ) ); ?>
                                    </span>
                                </td>
                                <td class="col-name">
                                    <strong><?php echo esc_html( $lead->nome_empresa ); ?></strong>
                                </td>
                                <td class="col-phone">
                                    <a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer" class="crm-wa-link" title="Abrir no WhatsApp">
                                        <span class="dashicons dashicons-whatsapp"></span>
                                        <?php echo esc_html( $lead->telefone_whatsapp ); ?>
                                    </a>
                                </td>
                                <td class="col-road">
                                    <?php echo ! empty( $lead->localizacao_rodovia ) ? esc_html( $lead->localizacao_rodovia ) : '<span class="crm-muted">-</span>'; ?>
                                </td>
                                <td class="col-status">
                                    <select class="crm-status-select status-<?php echo esc_attr( $status_slug ); ?>" data-id="<?php echo esc_attr( $lead->id ); ?>">
                                        <option value="Novo" <?php selected( $lead->status_lead, 'Novo' ); ?>>Novo</option>
                                        <option value="Em Contato" <?php selected( $lead->status_lead, 'Em Contato' ); ?>>Em Contato</option>
                                        <option value="Convertido" <?php selected( $lead->status_lead, 'Convertido' ); ?>>Convertido</option>
                                        <option value="Perdido" <?php selected( $lead->status_lead, 'Perdido' ); ?>>Perdido</option>
                                    </select>
                                </td>
                                <td class="col-notes">
                                    <div class="crm-note-box">
                                        <input type="text" class="crm-note-input" data-id="<?php echo esc_attr( $lead->id ); ?>" value="<?php echo esc_attr( $lead->notas ); ?>" placeholder="Adicionar nota...">
                                        <span class="crm-save-check dashicons dashicons-yes" title="Salvo"></span>
                                    </div>
                                </td>
                                <td class="col-actions">
                                    <button type="button" class="crm-btn-delete" data-id="<?php echo esc_attr( $lead->id ); ?>" title="Excluir Lead">
                                        <span class="dashicons dashicons-trash"></span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <!-- Pagination -->
        <?php if ( $total_pages > 1 ) : ?>
            <div class="crm-pagination">
                <?php
                echo paginate_links( array(
                    'base'      => add_query_arg( 'paged', '%#%' ),
                    'format'    => '',
                    'prev_text' => '&laquo; Anterior',
                    'next_text' => 'Próxima &raquo;',
                    'total'     => $total_pages,
                    'current'   => $current_page,
                ) );
                ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

