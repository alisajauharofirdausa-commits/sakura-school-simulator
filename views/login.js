document.getElementById('formLogin').addEventListener('submit', async (e) => {
    e.preventDefault();

    const usernameInput = document.getElementById('username').value;
    const passwordInput = document.getElementById('password').value;
    const errorBox = document.getElementById('errorMessage');

    errorBox.classList.add('hidden');

    try {
        const res = await fetch('controllers/auth.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username: usernameInput, password: passwordInput })
        });

        const data = await res.json();

        if (res.ok && data.success) {
            localStorage.setItem('user_inventory', JSON.stringify(data.user));
            window.location.href = 'index.html';
        } else {
            errorBox.innerText = data.message || "Login gagal!";
            errorBox.classList.remove('hidden');
        }
    } catch (err) {
        errorBox.innerText = "Terjadi kesalahan koneksi server.";
        errorBox.classList.remove('hidden');
    }
});

// Fitur Toggle Show/Hide Password
const passwordInput = document.getElementById('password');
const togglePasswordBtn = document.getElementById('togglePassword');

togglePasswordBtn.addEventListener('click', () => {
    const isPassword = passwordInput.getAttribute('type') === 'password';
    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
    togglePasswordBtn.classList.toggle('text-blue-600', isPassword);
    togglePasswordBtn.classList.toggle('text-slate-400', !isPassword);
});