<?php get_header(); ?>

    <main class="container text-center">
        <!-- Welcome Section -->
        <section class="py-5">
            <h2 class="display-5 fw-bold">Добре дошли във <?php bloginfo('name'); ?></h2>
            <p class="lead">Резервирайте следващия си час за фризьор онлайн за секунди.</p>
            <a href="/booking" class="btn btn-primary btn-lg mt-3">Запази си час</a>
        </section>

        <!-- Services Section -->
        <section class="py-5">
            <h3 class="mb-4">Нашите услуги</h3>
            <div class="row">
                <div class="col-md-4">
                    <div class="border p-3 h-100">
                        <img src="<?php echo get_template_directory_uri(); ?>/images/haircut.png" alt="Подстригване" class="img-fluid mb-3">
                        <h5><strong>Мъжко и женско подстригване</strong></h5>
                        <p>Свежи прически и стилизиране, съобразени с вас.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border p-3 h-100">
                        <img src="<?php echo get_template_directory_uri(); ?>/images/coloring.png" alt="Подстригване" class="img-fluid mb-3">
                        <h5><strong>Боядисване</strong></h5>
                        <p>Ярки нюанси и експертни услуги за боядисване на косата.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border p-3 h-100">
                        <img src="<?php echo get_template_directory_uri(); ?>/images/styling.png" alt="Стайлинг" class="img-fluid mb-3">
                        <h5><strong>Стайлинг</strong></h5>
                        <p>Стилизиране и прически за специални събития.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="border p-3 h-100">
                        <img src="<?php echo get_template_directory_uri(); ?>/images/manicurist.png" alt="Подстригване" class="img-fluid mb-3">
                        <h5><strong>Мъжко и женско подстригване</strong></h5>
                        <p>Свежи прически и стилизиране, съобразени с вас.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border p-3 h-100">
                        <img src="<?php echo get_template_directory_uri(); ?>/images/beautician.png" alt="Боядисване" class="img-fluid mb-3">
                        <h5><strong>Боядисване</strong></h5>
                        <p>Ярки нюанси и експертни услуги за боядисване на косата.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border p-3 h-100">
                        <img src="<?php echo get_template_directory_uri(); ?>/images/haircut.png" alt="Подстригване" class="img-fluid mb-3">
                        <h5><strong>Стайлинг</strong></h5>
                        <p>Стилизиране и прически за специални събития.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Us Section -->
        <section class="py-5 bg-light">
            <h3 class="mb-4">За нас и какво правим</h3>
            <p class="lead">
                Ние сме екип от опитни фризьори, които предоставят висококачествени услуги, съобразени с вашия стил. Работим със страст, внимание към детайла и винаги с усмивка.
            </p>
        </section>

        <!-- Testimonials Section -->
        <section class="py-5">
            <h3 class="mb-4">Какво казват нашите клиенти</h3>
            <blockquote class="blockquote">
                <p class="mb-0">"Най-доброто обслужване, което съм получавала! Препоръчвам с две ръце."</p>
                <footer class="blockquote-footer">Мария Иванова</footer>
            </blockquote>
            <blockquote class="blockquote mt-4">
                <p class="mb-0">"Прекрасен екип и страхотна атмосфера. Ще се върна отново!"</p>
                <footer class="blockquote-footer">Иван Петров</footer>
            </blockquote>
        </section>
    </main>


<?php get_footer(); ?>