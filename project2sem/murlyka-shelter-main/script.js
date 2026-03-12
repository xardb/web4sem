document.addEventListener('DOMContentLoaded', function() {
    console.log('Header initialized');
    const contactsModal = document.getElementById('contactsModal');
    const openContactsBtn = document.getElementById('openContacts');
    const closeContactsBtn = document.querySelector('.close-modal');

    function openModal() {
        contactsModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        contactsModal.style.display = 'none';
        document.body.style.overflow = '';
    }

    if (openContactsBtn && contactsModal) {
        openContactsBtn.addEventListener('click', openModal);
    }

    if (closeContactsBtn && contactsModal) {
        closeContactsBtn.addEventListener('click', closeModal);
    }

    if (contactsModal) {
        contactsModal.addEventListener('click', function (e) {
            if (e.target === contactsModal) {
                closeModal();
            }
        });
    }
    const formModal = document.getElementById('formModal');
    const openFormBtn = document.getElementById('openForm');
    const closeFormBtn = document.getElementById('closeForm');

    function openFormModal(clearForm = true) {
        formModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        if (clearForm) {
            const form = document.getElementById('contactForm');
            if (form) form.reset();
        }
    }

    function closeFormModal() {
        formModal.style.display = 'none';
        document.body.style.overflow = '';
    }

    // Hero кнопка — с очисткой
    if (openFormBtn && formModal) {
        openFormBtn.addEventListener('click', () => openFormModal(true));
    }

    // Кнопка закрытия
    if (closeFormBtn && formModal) {
        closeFormBtn.addEventListener('click', closeFormModal);
    }

    // Клик по фону
    if (formModal) {
        formModal.addEventListener('click', function (e) {
            if (e.target === formModal) closeFormModal();
        });
    }

    // Кнопки кошек — БЕЗ очистки
    document.querySelectorAll('.cat-btn').forEach(button => {
        button.addEventListener('click', function () {
            const catName = this.dataset.cat;
            const catAge = this.dataset.age;

            const messageField = document.getElementById('message');
            if (messageField) {
                messageField.value = `Интересует ${catName} (${catAge})`;
            }

            openFormModal(false);
        });
    });
    const contactForm = document.getElementById('contactForm');
    const submitBtn = document.querySelector('.submit-btn');
    const formMessage = document.getElementById('formMessage');

    if (contactForm) {
        contactForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            submitBtn.disabled = true;
            submitBtn.textContent = 'Отправляем...';
            formMessage.style.display = 'none';

            try {
                const formData = new FormData(contactForm);

                const response = await fetch('../task05/form.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.status === 'success') {
                    formMessage.textContent = 'Заявка отправлена! Мы свяжемся с вами.';
                    formMessage.className = 'form-message success';
                    formMessage.style.display = 'block';
                    contactForm.reset();

                    setTimeout(() => {
                        closeFormModal();
                        formMessage.style.display = 'none';
                    }, 3000);
                } else {
                    formMessage.textContent = 'Ошибка: ' + (result.message || 'Не удалось отправить форму.');
                    formMessage.className = 'form-message error';
                    formMessage.style.display = 'block';
                }

            } catch (error) {
                formMessage.textContent = 'Ошибка сети. Попробуйте позже.';
                formMessage.className = 'form-message error';
                formMessage.style.display = 'block';
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Отправить заявку';
            }
        });
    }

    const dropdownItems = document.querySelectorAll('.dropdown-menu .dropdown-item');
    dropdownItems.forEach(item => item.classList.remove('active'));
    const navbarCollapse = document.getElementById('mainNavbar');
    const navLinks = document.querySelectorAll('#mainNavbar a[href^="#"]');

    navLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href'); // типа "#cats"
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                e.preventDefault(); // отменяем стандартный скролл

                // плавно скроллим сами
                targetElement.scrollIntoView({behavior: 'smooth'});

                // закрываем бургер, если он открыт и экран маленький
                if (window.innerWidth < 992 && navbarCollapse.classList.contains('show')) {
                    const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                    if (bsCollapse) bsCollapse.hide();
                }
            }
        });
    });

});
