
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    const loginForm = document.getElementById('loginForm');
    if (registerForm) {
        registerForm.addEventListener('submit', handleRegister);
    }
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }
});
async function handleRegister(e) {
    e.preventDefault();
    const errorMessage = document.getElementById('errorMessage');
    errorMessage.classList.remove('show');
    const formData = new FormData(e.target);
    try {
        const response = await fetch('/api/register.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            window.location.href = '/';
        } else {
            errorMessage.textContent = data.message;
            errorMessage.classList.add('show');
        }
    } catch (error) {
        errorMessage.textContent = 'Ошибка соединения с сервером';
        errorMessage.classList.add('show');
    }
}
async function handleLogin(e) {
    e.preventDefault();
    const errorMessage = document.getElementById('errorMessage');
    errorMessage.classList.remove('show');
    const formData = new FormData(e.target);
    try {
        const response = await fetch('/api/login.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            window.location.href = '/';
        } else {
            errorMessage.textContent = data.message;
            errorMessage.classList.add('show');
        }
    } catch (error) {
        errorMessage.textContent = 'Ошибка соединения с сервером';
        errorMessage.classList.add('show');
    }
}