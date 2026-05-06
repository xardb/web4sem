document.addEventListener('DOMContentLoaded', function () {
    console.log('Header initialized');

    const contactsModal = document.getElementById('contactsModal');
    const openContactsBtn = document.getElementById('openContacts');
    const closeContactsBtn = document.querySelector('.close-modal');

    function openModal() {
        if (!contactsModal) return;
        contactsModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        if (!contactsModal) return;
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
        if (!formModal) return;

        formModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        if (clearForm) {
            const form = document.getElementById('contactForm');
            if (form) form.reset();

            const formMessage = document.getElementById('formMessage');
            if (formMessage) {
                formMessage.style.display = 'none';
                formMessage.className = 'form-message';
                formMessage.textContent = '';
            }
        }
    }

    function closeFormModal() {
        if (!formModal) return;

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
            if (e.target === formModal) {
                closeFormModal();
            }
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

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Отправляем...';
            }

            if (formMessage) {
                formMessage.style.display = 'none';
                formMessage.className = 'form-message';
                formMessage.textContent = '';
            }

            try {
                const formData = new FormData(contactForm);

                const response = await fetch(contactForm.action, {
                    method: contactForm.method || 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const result = await response.json();

                if (!response.ok || result.status !== 'success') {
                    const errorMessage = result.message || 'Не удалось отправить форму.';

                    if (formMessage) {
                        formMessage.textContent = 'Ошибка: ' + errorMessage;
                        formMessage.className = 'form-message error';
                        formMessage.style.display = 'block';
                    } else {
                        alert('Ошибка: ' + errorMessage);
                    }

                    return;
                }

                const login = result.login || '';
                const password = result.password || '';
                const profileUrl = result.profile_url || '';

                if (formMessage) {
                    formMessage.innerHTML =
                        'Заявка отправлена успешно!<br>' +
                        '<strong>Логин:</strong> ' + login + '<br>' +
                        '<strong>Пароль:</strong> ' + password + '<br>' +
                        '<strong>Профиль:</strong> <a href="' + profileUrl + '">открыть</a>';

                    formMessage.className = 'form-message success';
                    formMessage.style.display = 'block';
                } else {
                    alert(
                        'Заявка отправлена успешно!\n\n' +
                        'Логин: ' + login + '\n' +
                        'Пароль: ' + password + '\n' +
                        'Профиль: ' + profileUrl
                    );
                }

                contactForm.reset();

            } catch (error) {
                if (formMessage) {
                    formMessage.textContent = 'Ошибка сети или сервера. Попробуйте позже.';
                    formMessage.className = 'form-message error';
                    formMessage.style.display = 'block';
                } else {
                    alert('Ошибка сети или сервера. Попробуйте позже.');
                }
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Отправить заявку';
                }
            }
        });
    }

    const dropdownItems = document.querySelectorAll('.dropdown-menu .dropdown-item');
    dropdownItems.forEach(item => item.classList.remove('active'));

    const navbarCollapse = document.getElementById('mainNavbar');
    const navLinks = document.querySelectorAll('#mainNavbar a[href^="#"]');

    navLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                e.preventDefault();

                targetElement.scrollIntoView({ behavior: 'smooth' });

                if (
                    window.innerWidth < 992 &&
                    navbarCollapse &&
                    navbarCollapse.classList.contains('show') &&
                    typeof bootstrap !== 'undefined'
                ) {
                    const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                    if (bsCollapse) bsCollapse.hide();
                }
            }
        });
    });
});