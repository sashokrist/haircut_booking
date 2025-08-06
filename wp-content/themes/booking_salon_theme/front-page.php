<?php get_header(); ?>

<main class="container text-center">
    <section class="py-5">
        <h2 class="display-5 fw-bold">Welcome to <?php bloginfo('name'); ?></h2>
        <p class="lead">Book your next hair appointment online in seconds.</p>
        <a href="/booking" class="btn btn-primary btn-lg mt-3">Book Now</a>
    </section>

    <section class="py-5">
        <h3>Our Services</h3>
        <div class="row mt-4">
            <div class="col-md-4">
                <h5>Haircut</h5>
                <p>Fresh cuts and styling tailored to you.</p>
            </div>
            <div class="col-md-4">
                <h5>Coloring</h5>
                <p>Vibrant shades and expert coloring services.</p>
            </div>
            <div class="col-md-4">
                <h5>Styling</h5>
                <p>Special event styling and blowouts.</p>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>