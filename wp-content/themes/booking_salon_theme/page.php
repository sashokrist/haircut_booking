<?php get_header(); ?>

<main class="container">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <article class="mb-5">
            <h1 class="mb-4"><?php the_title(); ?></h1>
            <div class="page-content">
                <?php the_content(); ?>
            </div>
        </article>
    <?php endwhile; else: ?>
        <p><?php esc_html_e('Sorry, the page you are looking for could not be found.', 'salon-theme'); ?></p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>