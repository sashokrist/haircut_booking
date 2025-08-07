<?php get_header(); ?>

<main class="container text-center">
    <section class="py-5">
        <h2 class="display-5 fw-bold">Добре дошли във <?php bloginfo('name'); ?></h2>
        <p class="lead">Резервирайте следващия си час за фризьор онлайн за секунди.</p>
        <a href="/booking" class="btn btn-primary btn-lg mt-3">Запази си час</a>
    </section>

    <section class="py-5">
        <h3>Нашите услуги</h3>
        <div class="row mt-4">
            <div class="col-md-4">
                <h5>Мъжко и женско подстригване</h5>
                <p>Свежи прически и стилизиране, съобразени с вас.</p>
            </div>
            <div class="col-md-4">
                <h5>Боядисване</h5>
                <p>Ярки нюанси и експертни услуги за боядисване на косата.</p>
            </div>
            <div class="col-md-4">
                <h5>Стайлинг</h5>
                <p>Стилизиране и прически за специални събития.</p>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>