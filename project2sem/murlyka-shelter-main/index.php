<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Приют кошек "Мурлыка"</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body data-bs-spy="scroll" data-bs-target=".main-nav-link" data-bs-offset="80" tabindex="0">
<header>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top custom-navbar">
        <div class="container">
            <a class="navbar-brand logo" href="#home">Приют <span>Мурлыка</span></a>

            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                    aria-controls="mainNavbar" aria-expanded="false" aria-label="Переключить навигацию">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link main-nav-link" href="#home">Главная</a></li>
                    <li class="nav-item"><a class="nav-link main-nav-link" href="#gallery">Подопечные</a></li>
                    <li class="nav-item"><a class="nav-link main-nav-link" href="#about">О нас</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="catsDropdown"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Кошки
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="catsDropdown">
                            <li><a class="dropdown-item" href="#cats">Все кошки</a></li>
                            <li><a class="dropdown-item" href="#kittens">Котята</a></li>
                            <li><a class="dropdown-item" href="#adults">Взрослые</a></li>
                            <li><a class="dropdown-item" href="#seniors">Пожилые</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link main-nav-link" href="#faq">FAQ</a></li>
                    <li class="nav-item">
                        <button class="btn btn-primary contact-btn" id="openContacts">Связаться</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>


<main>
    <section class="video-section" id="home">
        <div class="video-container">
            <video autoplay muted loop playsinline id="headerVideo">
                <source src="img/video.mp4" type="video/mp4">
                Ваш браузер не поддерживает видео.
            </video>
            <img src="img/background-mobile.jpg" alt="Приют Мурлыка"
                 class="back-image-mobile">
        </div>
        <div class="video-overlay"></div>
        <div class="video-content">
            <h1 class="video-title">Приют Мурлыка</h1>
            <p class="video-subtitle">Найдите своего пушистого друга. Каждая кошка ждёт дом.</p>
            <button class="contact-form-btn" id="openForm">Приютить кошку</button>
        </div>
    </section>
    <section id="gallery" class="py-5 bg-white">
        <div class="container">
            <h2 class="section-title mb-4">Наши подопечные</h2>

            <div id="catsCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">

                    <!-- Слайд 1 -->
                    <div class="carousel-item active">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <img src="img/cat01.jpg" class="card-img-top" alt="Мурка">
                                    <div class="card-body">
                                        <h5 class="card-title">Мурка</h5>
                                        <p class="card-text">3 месяца, игривая и ласковая.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 d-none d-md-block">
                                <div class="card h-100">
                                    <img src="img/cat02.jpeg" class="card-img-top" alt="Барсик">
                                    <div class="card-body">
                                        <h5 class="card-title">Барсик</h5>
                                        <p class="card-text">4 месяца, обожает играть с мячиками.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Слайд 2 -->
                    <div class="carousel-item">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <img src="img/cat03.jpg" class="card-img-top" alt="Соня">
                                    <div class="card-body">
                                        <h5 class="card-title">Соня</h5>
                                        <p class="card-text">2 года, спокойная домоседка.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 d-none d-md-block">
                                <div class="card h-100">
                                    <img src="img/cat04.jpg" class="card-img-top" alt="Рыжик">
                                    <div class="card-body">
                                        <h5 class="card-title">Рыжик</h5>
                                        <p class="card-text">3 года, очень общительный кот.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Слайд 3 -->
                    <div class="carousel-item">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <img src="img/cat05.jpeg" class="card-img-top" alt="Киса">
                                    <div class="card-body">
                                        <h5 class="card-title">Киса</h5>
                                        <p class="card-text">6 месяцев, любит сидеть на руках.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 d-none d-md-block">
                                <div class="card h-100">
                                    <img src="img/cat06.jpg" class="card-img-top" alt="Милка">
                                    <div class="card-body">
                                        <h5 class="card-title">Милка</h5>
                                        <p class="card-text">5 лет, идеально подойдёт для спокойного дома.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Слайд 4 -->
                    <div class="carousel-item">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <img src="img/cat07.jpg" class="card-img-top" alt="Бабушка">
                                    <div class="card-body">
                                        <h5 class="card-title">Бабушка</h5>
                                        <p class="card-text">10 лет, очень ласковая и тихая.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 d-none d-md-block">
                                <div class="card h-100">
                                    <img src="img/cat08.jpg" class="card-img-top" alt="Дедушка">
                                    <div class="card-body">
                                        <h5 class="card-title">Дедушка</h5>
                                        <p class="card-text">12 лет, любит спать на подушках.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Слайд 5 -->
                    <div class="carousel-item">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <img src="img/cat09.jpg" class="card-img-top" alt="Снежок">
                                    <div class="card-body">
                                        <h5 class="card-title">Снежок</h5>
                                        <p class="card-text">1 год, белый пушистый кот.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 d-none d-md-block">
                                <div class="card h-100">
                                    <img src="img/cat10.jpeg" class="card-img-top" alt="Уголёк">
                                    <div class="card-body">
                                        <h5 class="card-title">Уголёк</h5>
                                        <p class="card-text">2 года, черный кот с золотыми глазами.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#catsCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Предыдущий</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#catsCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Следующий</span>
                </button>
            </div>
        </div>
    </section>
    <section id="about" class="about-section">
        <div class="container">
            <h2 class="section-title">Наша миссия</h2>
            <div class="about-container">
                <p class="about-text">
                    Приют «Мурлыка» спасает бездомных кошек, лечит их и помогает найти новый дом.
                    Мы сотрудничаем с ветеринарными клиниками и волонтёрами по всему городу.
                </p>
                <p class="about-text">
                    Перед тем как кошка уедет к новым хозяевам, она проходит полный осмотр,
                    вакцинацию и стерилизацию. Мы также помогаем с консультациями по уходу.
                </p>
                <p class="about-text">
                    Вы можете выбрать кота на сайте и оставить заявку онлайн — мы свяжемся с вами,
                    чтобы договориться о встрече в приюте.
                </p>
                <p class="contact-person">Руководители приюта: Дьяченко В.Е. && Борисова М.М.</p>
            </div>
        </div>
    </section>
    <section id="cats" class="cats-section">
        <div class="container">

            <h3 id="kittens" class="category-title">Котята (до 1 года)</h3>
            <div class="cats-grid">
                <article class="cat-card">
                    <div class="cat-photo">
                        <img src="img/cat01.jpg" alt="Мурка">
                    </div>
                    <div class="cat-info">
                        <h4 class="cat-name">Мурка</h4>
                        <p class="cat-age">3 месяца • девочка</p>
                        <p class="cat-desc">Очень игривая, быстро привыкает к людям.</p>
                        <button class="cat-btn" data-cat="Мурка" data-age="3 месяца • девочка">Приютить</button>                    </div>
                </article>

                <article class="cat-card">
                    <div class="cat-photo">
                        <img src="img/cat02.jpeg" alt="Барсик">
                    </div>
                    <div class="cat-info">
                        <h4 class="cat-name">Барсик</h4>
                        <p class="cat-age">4 месяца • мальчик</p>
                        <p class="cat-desc">Обожает игрушки и бегать за лазерной указкой.</p>
                        <button class="cat-btn" data-cat="Барсик" data-age="4 месяца • мальчик">Приютить</button>                    </div>
                </article>

                <article class="cat-card">
                    <div class="cat-photo">
                        <img src="img/cat03.jpg" alt="Киса">
                    </div>
                    <div class="cat-info">
                        <h4 class="cat-name">Киса</h4>
                        <p class="cat-age">6 месяцев • девочка</p>
                        <p class="cat-desc">Ласковая, любит сидеть на руках.</p>
                        <button class="cat-btn" data-cat="Киса" data-age="6 месяцев • девочка">Приютить</button>                    </div>
                </article>
            </div>

            <h3 id="adults" class="category-title">Взрослые кошки (1–7 лет)</h3>
            <div class="cats-grid">
                <article class="cat-card">
                    <div class="cat-photo">
                        <img src="img/cat05.jpeg" alt="Соня">
                    </div>
                    <div class="cat-info">
                        <h4 class="cat-name">Соня</h4>
                        <p class="cat-age">2 года • девочка</p>
                        <p class="cat-desc">Спокойная, отлично ладит с детьми.</p>
                        <button class="cat-btn" data-cat="Соня" data-age="2 года • девочка">Приютить</button>                    </div>
                </article>

                <article class="cat-card">
                    <div class="cat-photo">
                        <img src="img/cat04.jpg" alt="Рыжик">
                    </div>
                    <div class="cat-info">
                        <h4 class="cat-name">Рыжик</h4>
                        <p class="cat-age">3 года • мальчик</p>
                        <p class="cat-desc">Очень общительный, любит внимание.</p>
                        <button class="cat-btn" data-cat="Рыжик" data-age="3 года • мальчик">Приютить</button>                    </div>
                </article>

                <article class="cat-card">
                    <div class="cat-photo">
                        <img src="img/cat06.jpg" alt="Милка">
                    </div>
                    <div class="cat-info">
                        <h4 class="cat-name">Милка</h4>
                        <p class="cat-age">5 лет • девочка</p>
                        <p class="cat-desc">Ненавязчивая, идеальна для спокойной квартиры.</p>
                        <button class="cat-btn" data-cat="Милка" data-age="5 лет • девочка">Приютить</button>                    </div>
                </article>
            </div>

            <h3 id="seniors" class="category-title">Пожилые кошки (7+ лет)</h3>
            <div class="cats-grid">
                <article class="cat-card">
                    <div class="cat-photo">
                        <img src="img/cat07.jpg" alt="Бабушка">
                    </div>
                    <div class="cat-info">
                        <h4 class="cat-name">Бабушка</h4>
                        <p class="cat-age">10 лет • девочка</p>
                        <p class="cat-desc">Очень спокойная, оценит тишину и мягкий плед.</p>
                        <button class="cat-btn" data-cat="Бабушка" data-age="10 лет • девочка">Приютить</button>                    </div>
                </article>

                <article class="cat-card">
                    <div class="cat-photo">
                        <img src="img/cat08.jpg" alt="Дедушка">
                    </div>
                    <div class="cat-info">
                        <h4 class="cat-name">Дедушка</h4>
                        <p class="cat-age">12 лет • мальчик</p>
                        <p class="cat-desc">Любит спать на подушках и громко мурлыкать.</p>
                        <button class="cat-btn" data-cat="Дедушка" data-age="12 лет • мальчик">Приютить</button>                    </div>
                </article>
            </div>

        </div>
    </section>
    <section id="faq" class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title mb-4">FAQ — часто задаваемые вопросы</h2>

            <div class="accordion" id="faqAccordion">
                <!-- Вопрос 1 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faq-heading-one">
                        <button class="accordion-button collapsed" type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#faq-collapse-one"
                                aria-expanded="false"
                                aria-controls="faq-collapse-one">
                            Какие условия пристройства кошки?
                        </button>
                    </h2>
                    <div id="faq-collapse-one" class="accordion-collapse collapse"
                         aria-labelledby="faq-heading-one" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Мы заключаем договор передачи животного, просим показать условия проживания
                            и оставаться на связи в первые месяцы после пристройства.
                        </div>
                    </div>
                </div>

                <!-- Вопрос 2 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faq-heading-two">
                        <button class="accordion-button collapsed" type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#faq-collapse-two"
                                aria-expanded="false"
                                aria-controls="faq-collapse-two">
                            Делаете ли вы прививки и стерилизацию?
                        </button>
                    </h2>
                    <div id="faq-collapse-two" class="accordion-collapse collapse"
                         aria-labelledby="faq-heading-two" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Все взрослые кошки привиты и стерилизованы, котята — по возрасту.
                            Подскажем график дальнейших прививок и осмотров у ветеринара.
                        </div>
                    </div>
                </div>

                <!-- Вопрос 3 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faq-heading-three">
                        <button class="accordion-button collapsed" type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#faq-collapse-three"
                                aria-expanded="false"
                                aria-controls="faq-collapse-three">
                            Можно ли оформить временную передержку?
                        </button>
                    </h2>
                    <div id="faq-collapse-three" class="accordion-collapse collapse"
                         aria-labelledby="faq-heading-three" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Да, мы сотрудничаем с передержками. Расскажем о требованиях и поможем
                            подобрать котика, подходящего под ваши условия.
                        </div>
                    </div>
                </div>

                <!-- Вопрос 4 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faq-heading-four">
                        <button class="accordion-button collapsed" type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#faq-collapse-four"
                                aria-expanded="false"
                                aria-controls="faq-collapse-four">
                            Как проходит знакомство с кошкой?
                        </button>
                    </h2>
                    <div id="faq-collapse-four" class="accordion-collapse collapse"
                         aria-labelledby="faq-heading-four" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Мы договариваемся о времени визита в приют, знакомим вас с животным,
                            рассказываем о характере и особенностях, отвечаем на вопросы.
                        </div>
                    </div>
                </div>

                <!-- Вопрос 5 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faq-heading-five">
                        <button class="accordion-button collapsed" type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#faq-collapse-five"
                                aria-expanded="false"
                                aria-controls="faq-collapse-five">
                            Помогаете ли вы с доставкой кошки домой?
                        </button>
                    </h2>
                    <div id="faq-collapse-five" class="accordion-collapse collapse"
                         aria-labelledby="faq-heading-five" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            В пределах города можем помочь с доставкой за символический взнос
                            или порекомендуем проверенный зоотакси‑сервис.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
<script src="script.js"></script>
<div class="contacts-modal" id="contactsModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Контакты приюта</h2>
            <button class="close-modal" id="closeContacts">&times;</button>
        </div>
        <div class="contact-info">
            <div><i class="fas fa-phone"></i> +7 (495) 123-45-67</div>
            <div><i class="fas fa-envelope"></i> info@murlyka.ru</div>
            <div><i class="fas fa-map-marker-alt"></i> ул. Котовского, 25</div>
            <div><i class="fas fa-clock"></i> Пн–Вс: 10:00–19:00</div>
        </div>
    </div>
</div>
<div class="form-modal" id="formModal">
    <div class="form-content">
        <div class="modal-header">
            <h2 class="modal-title">Заявка на пристройство</h2>
            <button class="close-modal" id="closeForm">&times;</button>
        </div>
        <form id="contactForm" action="../task05/form.php" method="POST">
            <div class="form-group">
                <label for="fullName">ФИО *</label>
                <input type="text" id="fullName" name="fullName" required>
            </div>
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Телефон *</label>
                <input
                        type="tel"
                        id="phone"
                        name="phone"
                        required
                        pattern="\+?[0-9\s\-\(\)]{6,}"
                        title="Введите телефон, можно использовать цифры, +, пробелы, -, ()">

            </div>
            <div class="form-group">
                <label for="message">Информация о кошке / вопрос</label>
                <textarea id="message" name="message"
                          placeholder="Например: интересует котёнок Мурка, есть опыт содержания 5 лет."></textarea>
            </div>
            <div class="form-group checkbox-group">
                <input type="checkbox" id="privacy" name="privacy" required>
                <label for="privacy">Согласен(а) на обработку персональных данных</label>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit" class="submit-btn">Отправить заявку</button>
            <div class="form-message" id="formMessage"></div>
        </form>
    </div>
</div>
<footer class="site-footer">
    <div class="container footer-container">
        <div class="footer-left">
            <h3 class="footer-logo">Приют <span>Мурлыка</span></h3>
            <p class="footer-text">
                Помогаем кошкам найти дом. Вы можете поддержать приют пожертвованием или стать волонтёром.
            </p>
        </div>

        <div class="footer-middle">
            <h4 class="footer-title">Контакты</h4>
            <ul class="footer-list">
                <li>Телефон: +7 (495) 123-45-67</li>
                <li>Email: info@murlyka.ru</li>
                <li>Адрес: ул. Котовского, 25</li>
            </ul>
        </div>

        <div class="footer-right">
            <h4 class="footer-title">Навигация</h4>
            <ul class="footer-list">
                <li><a href="#home">Главная</a></li>
                <li><a href="#gallery">Подопечные</a></li>
                <li><a href="#about">О нас</a></li>
                <li><a href="#cats">Кошки</a></li>
                <li><a href="#faq">FAQ</a></li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container footer-bottom-inner">
            <span>© 2025 Приют «Мурлыка»</span>
            <span>Сайт сделан в рамках семестрового проекта по WEBу</span>
        </div>
    </div>
</footer>

</body>
</html>
