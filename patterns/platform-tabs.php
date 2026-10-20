<?php
/**
 * Title: Abas da Plataforma Acione Mobile
 * Slug: acionemobile/platform-tabs
 * Categories: acione-mobile
 * Description: Interactive 4-tab platform breakdown (Na Rodovia, Na Transportadora, Na Rede Prestadora, Central 24h).
 */
$theme_uri = get_template_directory_uri();
?>
<!-- wp:group {"tagName":"section","className":"platform-tabs-section","layout":{"type":"default"}} -->
<section class="platform-tabs-section" id="prestadores">
  <div class="section-header-compact">
    <div class="tech-label section-pretitle">ECOSSISTEMA INTEGRADO</div>
    <h2 class="section-h2">
      Uma única plataforma. Soluções completas para cada ponta da operação.
    </h2>
  </div>

  <!-- Tab Buttons Controls -->
  <div class="tabs-controls" role="tablist">
    <button class="tab-btn active" data-tab="tab-estrada" role="tab" aria-selected="true">
      01 • NA RODOVIA (MOTORISTA)
    </button>
    <button class="tab-btn" data-tab="tab-transportadora" role="tab" aria-selected="false">
      02 • NA TRANSPORTADORA (GESTOR)
    </button>
    <button class="tab-btn" data-tab="tab-prestador" role="tab" aria-selected="false">
      03 • NA REDE DE PRESTADORES
    </button>
    <button class="tab-btn" data-tab="tab-central" role="tab" aria-selected="false">
      04 • CENTRAL DE OPERAÇÕES 24H
    </button>
  </div>

  <!-- Tab 1: Na Estrada -->
  <div class="tab-pane active" id="tab-estrada" role="tabpanel">
    <div>
      <div class="tech-badge-lime" style="margin-bottom: 1rem;">
        <span class="pulse-dot"></span> APLICATIVO DO MOTORISTA
      </div>
      <h3 class="tab-info-title">
        Socorro imediato na palma da mão, sem burocracia ou pânico.
      </h3>
      <p class="tab-info-desc">
        Quando um pneu fura ou o motor falha no acostamento, o motorista não precisa procurar oficinas no Google ou pedir favores. Com um toque no app Acione Mobile ou mensagem na central, o chamado é aberto com coordenadas precisas.
      </p>
      <ul class="tab-features-list">
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
          <strong>Geolocalização Satelital Automática:</strong> Não precisa saber o KM exato.
        </li>
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
          <strong>Status em Tempo Real:</strong> Acompanhe a aproximação do socorrista no mapa.
        </li>
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
          <strong>Segurança Pessoal:</strong> Canal de emergência com a torre de controle.
        </li>
      </ul>
      <a href="#solicitar" class="btn-header btn-lime open-modal-trigger">TESTAR APLICATIVO</a>
    </div>
    <div class="tab-graphic-card">
      <img src="<?php echo esc_url( $theme_uri . '/assets/images/hero-truck.jpg' ); ?>" alt="Motorista acionando socorro">
    </div>
  </div>

  <!-- Tab 2: Na Transportadora -->
  <div class="tab-pane" id="tab-transportadora" role="tabpanel">
    <div>
      <div class="tech-badge-lime" style="margin-bottom: 1rem;">
        <span class="pulse-dot"></span> PAINEL DA TRANSPORTADORA
      </div>
      <h3 class="tab-info-title">
        Visibilidade completa da frota e redução drástica do tempo de parada.
      </h3>
      <p class="tab-info-desc">
        Os gestores de logística acompanham em uma tela todas as manutenções em trânsito. Chega de pagar valores abusivos de emergência: todos os prestadores cobram pela tabela padronizada da plataforma.
      </p>
      <ul class="tab-features-list">
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
          <strong>Auditoria de Serviços:</strong> Checklist fotográfico antes e depois do reparo.
        </li>
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
          <strong>Faturamento Centralizado:</strong> Relatório unificado e boleto quinzenal/mensal.
        </li>
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
          <strong>Integração com TMS e ERP:</strong> Conexão via API com seu sistema interno.
        </li>
      </ul>
      <a href="#solicitar" class="btn-header btn-lime open-modal-trigger">AGENDAR DEMONSTRAÇÃO</a>
    </div>
    <div class="tab-graphic-card">
      <img src="<?php echo esc_url( $theme_uri . '/assets/images/events.png' ); ?>" alt="Painel do gestor de frota">
    </div>
  </div>

  <!-- Tab 3: Na Rede de Prestadores -->
  <div class="tab-pane" id="tab-prestador" role="tabpanel">
    <div>
      <div class="tech-badge-lime" style="margin-bottom: 1rem;">
        <span class="pulse-dot"></span> APP DO PRESTADOR CREDENCIADO
      </div>
      <h3 class="tab-info-title">
        Demanda constante de serviços e garantia de pagamento sem inadimplência.
      </h3>
      <p class="tab-info-desc">
        Borracheiros, mecânicos pesados e operadores de guincho recebem solicitações diretas das maiores transportadoras do Brasil. Sem intermediários, sem atraso de pagamento: receba direto na sua conta.
      </p>
      <ul class="tab-features-list">
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
          <strong>Chamados na sua Rota:</strong> Receba serviços num raio de até 60 km.
        </li>
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
          <strong>Garantia de Pagamento:</strong> O valor do serviço é reservado na abertura.
        </li>
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
          <strong>Crescimento do Negócio:</strong> Clientes corporativos recorrentes para sua oficina.
        </li>
      </ul>
      <a href="#prestador" class="btn-header btn-lime open-modal-trigger">QUERO SER PRESTADOR</a>
    </div>
    <div class="tab-graphic-card">
      <img src="<?php echo esc_url( $theme_uri . '/assets/images/dock-doors.png' ); ?>" alt="Prestador credenciado Acione Mobile">
    </div>
  </div>

  <!-- Tab 4: Central de Operações 24h -->
  <div class="tab-pane" id="tab-central" role="tabpanel">
    <div>
      <div class="tech-badge-lime" style="margin-bottom: 1rem;">
        <span class="pulse-dot"></span> SUPORTE CRÍTICO 24/7
      </div>
      <h3 class="tab-info-title">
        Engenharia operacional e inteligência humana de prontidão para a sua carga.
      </h3>
      <p class="tab-info-desc">
        Nossa central própria conta com profissionais com décadas de experiência em estradas e socorro mecânico. Se o caso exigir peças especiais, transbordo ou escolta, atuamos como braço direito da transportadora.
      </p>
      <ul class="tab-features-list">
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
          <strong>Atendimento em Menos de 3 Minutos:</strong> SLA rigoroso de resposta.
        </li>
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
          <strong>Auditoria Especializada:</strong> Validação técnica de diagnósticos e laudos.
        </li>
        <li>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
          <strong>Cobertura em Rodovias Federais e Estaduais:</strong> De norte a sul do Brasil.
        </li>
      </ul>
      <a href="#solicitar" class="btn-header btn-lime open-modal-trigger">FALAR COM A CENTRAL</a>
    </div>
    <div class="tab-graphic-card">
      <img src="<?php echo esc_url( $theme_uri . '/assets/images/yard.png' ); ?>" alt="Central de Operacoes Acione Mobile">
    </div>
  </div>
</section>
<!-- /wp:group -->
