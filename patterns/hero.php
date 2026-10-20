<?php
/**
 * Title: Hero Acione Mobile
 * Slug: acionemobile/hero
 * Categories: acione-mobile, banner
 * Description: Hero fixa (pinned) com sequência de 410 frames WebP desenhada em canvas e controlada pelo scroll e títulos revelados letra a letra — clone do "video-carousel" do Terminal Industries.
 */
$theme_uri = get_template_directory_uri();

$titles = array(
	'Reinventamos o socorro rodoviário para o transporte de cargas.',
	'Tecnologia que liga o caminhão parado ao prestador certo, em minutos...',
	'em um só sistema conectado e automatizado.',
);
?>
<!-- wp:html -->
<section class="hero-seq" id="hero-seq" aria-label="Acione Mobile — socorro rodoviário inteligente">
	<div class="hero-seq-stage">
		<div class="hero-seq-media" aria-hidden="true">
			<canvas class="hero-seq-canvas" id="hero-canvas"
				data-desktop="<?php echo esc_url( $theme_uri . '/assets/frames/desktop/hero_anim_desktop_60_' ) . '{index}.webp'; ?>"
				data-desktop-base="<?php echo esc_url( $theme_uri . '/assets/frames/desktop/hero_anim_desktop_60_' ); ?>"
				data-desktop-count="410"
				data-mobile="<?php echo esc_url( $theme_uri . '/assets/frames/mobile/hero_anim_mobile_60_' ) . '{index}.webp'; ?>"
				data-mobile-base="<?php echo esc_url( $theme_uri . '/assets/frames/mobile/hero_anim_mobile_60_' ); ?>"
				data-mobile-count="409"></canvas>
		</div>

		<div class="hero-seq-scroll-hint label-mono"><span class="pulse-dot lime"></span> ROLE PARA EXPLORAR</div>

		<h1 class="screen-reader-text">Acione Mobile: socorro rodoviário para o transporte de cargas</h1>

		<div class="hero-seq-titles">
			<?php foreach ( $titles as $i => $title ) : ?>
				<h2 class="hero-seq-title" data-title="<?php echo (int) $i; ?>" aria-label="<?php echo esc_attr( $title ); ?>">
					<?php
					$words = explode( ' ', $title );
					foreach ( $words as $word ) {
						echo '<span class="hs-word" aria-hidden="true">';
						foreach ( preg_split( '//u', $word, -1, PREG_SPLIT_NO_EMPTY ) as $ch ) {
							echo '<span class="hs-ch">' . esc_html( $ch ) . '</span>';
						}
						echo '</span> ';
					}
					?>
				</h2>
			<?php endforeach; ?>
		</div>

		<div class="hero-seq-cta">
			<a href="#solicitar" class="btn-header btn-lime btn-large open-modal-trigger">SOLICITAR SOCORRO AGORA</a>
			<a href="#transportadoras" class="btn-header btn-secondary-white btn-large open-modal-trigger">CADASTRAR TRANSPORTADORA</a>
			<a href="#prestadores" class="btn-header btn-frosted btn-large open-modal-trigger">SEJA UM PRESTADOR</a>
		</div>

		<div class="hero-seq-progress" aria-hidden="true"><span id="hero-progress-bar"></span></div>
	</div>
</section>
<!-- /wp:html -->
