<?php
/**
 * Fallback template file for Acione Mobile block theme.
 *
 * @package AcioneMobile
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php
// WordPress Block Themes render templates from the /templates directory.
if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();
        the_content();
    }
}
wp_footer();
?>
</body>
</html>
