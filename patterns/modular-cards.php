<?php
/**
 * Title: Cards Modulares de Serviços
 * Slug: acionemobile/modular-cards
 * Categories: acione-mobile
 * Description: 3-column modular service cards (01 Mecanico, 02 Borracharia, 03 Auto-Eletrico & Guincho).
 */
$theme_uri = get_template_directory_uri();
?>
<!-- wp:group {"tagName":"section","className":"modular-cards-section","layout":{"type":"default"}} -->
<section class="modular-cards-section" id="solucao">
  <div class="section-header-compact">
    <div class="tech-label section-pretitle">SERVIÇOS DE PRONTA RESPOSTA RODOVIÁRIA</div>
    <h2 class="section-h2">
      Resolva o problema do seu caminhão agora. Sem burocracia, sem surpresas na fatura.
    </h2>
  </div>

  <div class="cards-grid-3">
    <!-- Card 1 -->
    <div class="modular-card">
      <div>
        <div class="modular-card-tag">01 • SOCORRO MECÂNICO</div>
        <h3 class="modular-card-title">Mecânica Pesada no Acostamento</h3>
        <p class="modular-card-desc">
          Diagnóstico e reparo emergencial de motor, sistema de freios, linha de ar comprimido, cuícas, correias e arrefecimento. Técnicos com oficinas volantes equipadas para resolver no local.
        </p>
      </div>
      <div class="modular-card-img-wrap">
        <img src="<?php echo esc_url( $theme_uri . '/assets/images/gate-entry.jpg' ); ?>" alt="Socorro Mecanico Pesado">
      </div>
      <a href="#solicitar-mecanico" class="modular-card-btn open-modal-trigger">
        SOLICITAR MECÂNICO 24H
      </a>
    </div>

    <!-- Card 2 -->
    <div class="modular-card">
      <div>
        <div class="modular-card-tag">02 • BORRACHARIA MÓVEL</div>
        <h3 class="modular-card-title">Borracharia Expressa 24 Horas</h3>
        <p class="modular-card-desc">
          Reparo a quente, vulcanização móvel, substituição de pneus e rodízio em qualquer rodovia do país. Chegada rápida para evitar que o motorista fique exposto em áreas de risco.
        </p>
      </div>
      <div class="modular-card-img-wrap">
        <img src="<?php echo esc_url( $theme_uri . '/assets/images/yard.png' ); ?>" alt="Borracharia Movel para Caminhoes">
      </div>
      <a href="#solicitar-borracharia" class="modular-card-btn open-modal-trigger">
        ACIONAR BORRACHARIA
      </a>
    </div>

    <!-- Card 3 -->
    <div class="modular-card">
      <div>
        <div class="modular-card-tag">03 • ELÉTRICO & GUINCHO</div>
        <h3 class="modular-card-title">Auto-Elétrico e Reboque Pesado</h3>
        <p class="modular-card-desc">
          Troca e recarga de baterias 24V, reparo de alternador, motor de partida e chicotes elétricos. Se necessário, guincho pesado lança ou plataforma para cavalos e composições bitrem.
        </p>
      </div>
      <div class="modular-card-img-wrap">
        <img src="<?php echo esc_url( $theme_uri . '/assets/images/network-yard.webp' ); ?>" alt="Guincho Pesado e Auto Eletrico">
      </div>
      <a href="#solicitar-guincho" class="modular-card-btn open-modal-trigger">
        SOLICITAR GUINCHO PESADO
      </a>
    </div>
  </div>
</section>
<!-- /wp:group -->
