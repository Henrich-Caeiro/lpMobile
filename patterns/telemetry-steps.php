<?php
/**
 * Title: Telemetria & Passos de Execução
 * Slug: acionemobile/telemetry-steps
 * Categories: acione-mobile
 * Description: Interactive HUD execution telemetry with video player and dynamic step switcher.
 */
$theme_uri = get_template_directory_uri();
?>
<!-- wp:group {"tagName":"section","className":"telemetry-steps-section","layout":{"type":"default"}} -->
<section class="telemetry-steps-section" id="plataforma">
  <div class="telemetry-layout">
    <!-- Left Column: Steps Navigation & Content -->
    <div>
      <div class="step-indicator-row">
        <span class="step-odometer">// TELEMETRIA EM TEMPO REAL</span>
        <span class="tech-badge-lime">
          <span class="pulse-dot"></span> ATENDIMENTO INTEGRADO
        </span>
      </div>

      <h2 class="telemetry-step-title">
        Detecção e Abertura em 15 Segundos
      </h2>

      <p class="telemetry-step-desc">
        Motorista aciona pelo app ou central com 1 clique. O sistema capta geolocalização exata na rodovia (KM, sentido, acostamento) e categoriza a pane automaticamente.
      </p>

      <!-- Step List Navigation -->
      <div class="step-nav-list">
        <div class="step-nav-item active" data-step="0">
          <div class="step-nav-num">01</div>
          <div class="step-nav-label">Detecção e Abertura do Chamado</div>
        </div>

        <div class="step-nav-item" data-step="1">
          <div class="step-nav-num">02</div>
          <div class="step-nav-label">Roteamento Inteligente do Prestador</div>
        </div>

        <div class="step-nav-item" data-step="2">
          <div class="step-nav-num">03</div>
          <div class="step-nav-label">Acompanhamento e Trajeto ao Vivo</div>
        </div>

        <div class="step-nav-item" data-step="3">
          <div class="step-nav-num">04</div>
          <div class="step-nav-label">Laudo Digital e Faturamento Homologado</div>
        </div>
      </div>
    </div>

    <!-- Right Column: Interactive Telemetry HUD Video Canvas -->
    <div class="telemetry-screen-wrapper">
      <div class="telemetry-top-bar">
        <div class="tech-title">
          <span class="pulse-dot lime"></span> CHAMADO EM ABERTO • LOCALIZADO
        </div>
        <div class="tech-label" style="color: rgba(255,255,255,0.6);">HUD RADAR V2.4</div>
      </div>

      <div class="telemetry-video-container">
        <video autoplay muted loop playsinline>
          <source src="<?php echo esc_url( $theme_uri . '/assets/videos/vid_3-1.mp4' ); ?>" type="video/mp4">
        </video>

        <!-- Technical HUD Overlay Grid & Crosshairs -->
        <div class="telemetry-hud-overlay">
          <div class="telemetry-crosshair ch-tl"></div>
          <div class="telemetry-crosshair ch-tr"></div>
          <div class="telemetry-crosshair ch-bl"></div>
          <div class="telemetry-crosshair ch-br"></div>
          <div class="telemetry-live-coords">
            LAT: -23.5489° S | LNG: -46.6388° W | KM 284 BR-116
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- /wp:group -->
