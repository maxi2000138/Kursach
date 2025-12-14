// Автофокус на поле кода
document.addEventListener('DOMContentLoaded', function() {
    const codeInput = document.getElementById('code');
    if (codeInput) {
        codeInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
            if (this.value.length === 6) {
                // Добавляем небольшую задержку перед отправкой
                setTimeout(() => {
                    this.form.submit();
                }, 300);
            }
        });
        
        // Автофокус при загрузке
        codeInput.focus();
    }
    
    // Автозакрытие алертов с анимацией
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach((alert, index) => {
        setTimeout(() => {
            alert.style.transition = 'all 0.5s ease-out';
            alert.style.opacity = '0';
            alert.style.transform = 'translateX(-100%)';
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 500);
        }, 5000 + (index * 200));
    });
    
    // Анимация появления элементов
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Применяем анимацию к карточкам
    document.querySelectorAll('.profile-card, .auth-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.6s ease-out';
        observer.observe(card);
    });
    
    // Валидация форм в реальном времени
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input[required], textarea[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    validateField(this);
                }
            });
        });
    });
    
    // Плавная прокрутка для якорей
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Эффект ripple для кнопок
    document.querySelectorAll('.btn').forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
    
    // Подсветка активного поля формы
    const formInputs = document.querySelectorAll('.form-group input, .form-group textarea');
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
        
        if (input.value) {
            input.parentElement.classList.add('focused');
        }
    });
    
    // Анимация счетчика для чисел (если будут)
    const animateValue = (element, start, end, duration) => {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            element.textContent = Math.floor(progress * (end - start) + start);
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    };
    
    // Показываем/скрываем пароль
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
        const toggle = document.createElement('button');
        toggle.type = 'button';
        toggle.className = 'password-toggle';
        toggle.innerHTML = '👁️';
        toggle.setAttribute('aria-label', 'Показать пароль');
        
        const formGroup = input.parentElement;
        if (formGroup && formGroup.classList.contains('form-group')) {
            formGroup.appendChild(toggle);
            
            // Вычисляем правильную позицию относительно input
            const updatePosition = () => {
                const inputRect = input.getBoundingClientRect();
                const formGroupRect = formGroup.getBoundingClientRect();
                const inputTop = inputRect.top - formGroupRect.top;
                const inputHeight = inputRect.height;
                toggle.style.top = (inputTop + inputHeight / 2) + 'px';
                
                // Показываем иконку только после вычисления позиции
                requestAnimationFrame(() => {
                    toggle.classList.add('visible');
                });
            };
            
            // Используем requestAnimationFrame для правильного вычисления после рендера
            requestAnimationFrame(() => {
                updatePosition();
            });
            
            window.addEventListener('resize', updatePosition);
            
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                if (input.type === 'password') {
                    input.type = 'text';
                    toggle.innerHTML = '🙈';
                    toggle.setAttribute('aria-label', 'Скрыть пароль');
                } else {
                    input.type = 'password';
                    toggle.innerHTML = '👁️';
                    toggle.setAttribute('aria-label', 'Показать пароль');
                }
            });
        }
    });
});

// Функция валидации поля
function validateField(field) {
    const value = field.value.trim();
    const type = field.type;
    let isValid = true;
    let errorMessage = '';
    
    // Проверка на обязательность
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'Это поле обязательно для заполнения';
    }
    
    // Проверка email
    if (type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = 'Некорректный формат email';
        }
    }
    
    // Проверка пароля
    if (type === 'password' && value) {
        if (value.length < 8) {
            isValid = false;
            errorMessage = 'Пароль должен содержать минимум 8 символов';
        }
    }
    
    // Проверка совпадения паролей
    if (field.name === 'confirm_password' || field.name === 'new_password') {
        const passwordField = field.form.querySelector('input[type="password"][name="password"], input[type="password"][name="current_password"]');
        if (passwordField && passwordField.value !== value) {
            isValid = false;
            errorMessage = 'Пароли не совпадают';
        }
    }
    
    // Применяем стили
    if (isValid) {
        field.classList.remove('error');
        field.style.borderColor = '';
        removeErrorMessage(field);
    } else {
        field.classList.add('error');
        field.style.borderColor = '#ef4444';
        showErrorMessage(field, errorMessage);
    }
    
    return isValid;
}

// Показать сообщение об ошибке
function showErrorMessage(field, message) {
    removeErrorMessage(field);
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    errorDiv.style.cssText = 'color: #ef4444; font-size: 0.85rem; margin-top: 0.25rem; animation: slideIn 0.3s ease-out;';
    
    field.parentElement.appendChild(errorDiv);
}

// Удалить сообщение об ошибке
function removeErrorMessage(field) {
    const errorDiv = field.parentElement.querySelector('.field-error');
    if (errorDiv) {
        errorDiv.remove();
    }
}

// Добавляем стили для ripple эффекта
const style = document.createElement('style');
style.textContent = `
    .btn {
        position: relative;
        overflow: hidden;
    }
    
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple-animation 0.6s ease-out;
        pointer-events: none;
    }
    
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    .form-group.focused label {
        color: var(--primary);
    }
    
    input.error {
        border-color: #ef4444 !important;
        animation: shake 0.3s;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }
`;
document.head.appendChild(style);
