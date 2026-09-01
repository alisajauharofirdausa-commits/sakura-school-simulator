const API_BARANG = '../controllers/barang.php';
const API_GUDANG = '../controllers/gudang.php';
const API_VENDOR = '../controllers/vendor.php';
const API_AUTH = '../controllers/auth.php';

window.switchTab = function(tabName) {
    const contents = document.querySelectorAll('.tab-content');
    contents.forEach(content => content.classList.add('hidden'));

    const buttons = document.querySelectorAll('.tab-btn');
    buttons.forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white', 'shadow-sm');
        btn.classList.add('text-slate-600', 'hover:bg-slate-50');
    });

    const targetTab = document.getElementById(`tab-${tabName}`);
    if (targetTab) {
        targetTab.classList.remove('hidden');
    }

    const targetBtn = document.getElementById(`btn-${tabName}`);
    if (targetBtn) {
        targetBtn.classList.remove('text-slate-600', 'hover:bg-slate-50');
        targetBtn.classList.add('bg-blue-600', 'text-white', 'shadow-sm');
    }
};

document.addEventListener('DOMContentLoaded', async () => {
    await cekStatusLogin();
    loadDropdownsGudangVendor();
    loadDataBarang();

    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => loadDataBarang(e.target.value));
    }

    document.getElementById('formBarang')?.addEventListener('submit', handleTambahBarang);
    document.getElementById('formGudang')?.addEventListener('submit', handleTambahGudang);
    document.getElementById('formVendor')?.addEventListener('submit', handleTambahVendor);
});

async function cekStatusLogin() {
    try {
        const res = await fetch(API_AUTH);
        const data = await res.json();

        if (!data.logged_in) {
            window.location.href = 'login.html';
        } else {
            const userInfo = document.getElementById('userInfo');
            if (userInfo) userInfo.innerText = `👤 ${data.user.nama}`;

            const btnLogout = document.getElementById('btnLogout');
            if (btnLogout) btnLogout.addEventListener('click', handleLogout);
        }
    } catch (err) {
        console.warn('Perhatian autentikasi:', err);
    }
}

async function handleLogout() {
    if (!confirm('Apakah Anda yakin ingin keluar?')) return;
    await fetch(API_AUTH, { method: 'DELETE' });
    localStorage.removeItem('user_inventory');
    window.location.href = 'login.html';
}

async function loadDropdownsGudangVendor() {
    try {
        const resGudang = await fetch(API_GUDANG);
        const dataGudang = await resGudang.json();
        const selectGudang = document.getElementById('id_gudang');
        
        if (selectGudang && Array.isArray(dataGudang)) {
            selectGudang.innerHTML = '<option value="">-- Pilih Gudang --</option>';
            dataGudang.forEach(g => {
                selectGudang.innerHTML += `<option value="${g.id_gudang}">${g.nama_gudang} (${g.lokasi || '-'})</option>`;
            });
        }

        const resVendor = await fetch(API_VENDOR);
        const dataVendor = await resVendor.json();
        const selectVendor = document.getElementById('id_vendor');

        if (selectVendor && Array.isArray(dataVendor)) {
            selectVendor.innerHTML = '<option value="">-- Pilih Vendor --</option>';
            dataVendor.forEach(v => {
                selectVendor.innerHTML += `<option value="${v.id_vendor}">${v.nama}</option>`;
            });
        }
    } catch (err) {
        console.error('Gagal memuat dropdown:', err);
    }
}

async function loadDataBarang(keyword = '') {
    try {
        const url = keyword ? `${API_BARANG}?q=${encodeURIComponent(keyword)}` : API_BARANG;
        const res = await fetch(url);
        const dataBarang = await res.json();

        const tabelBody = document.getElementById('tabelInventory');
        if (!tabelBody) return;
        
        tabelBody.innerHTML = '';
        let stokHabisList = [];

        if (!Array.isArray(dataBarang) || dataBarang.length === 0) {
            tabelBody.innerHTML = `
                <tr>
                    <td colspan="7" class="p-4 text-center text-slate-400">Belum ada data barang.</td>
                </tr>
            `;
        } else {
            dataBarang.forEach(item => {
                if (parseInt(item.kuantitas_stok) <= 0) {
                    stokHabisList.push(item.nama_barang);
                }

                const formatHarga = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(item.harga);

                tabelBody.innerHTML += `
                    <tr class="hover:bg-slate-50 border-b">
                        <td class="p-3 font-medium text-slate-700">${item.serial_number}</td>
                        <td class="p-3 font-semibold text-slate-900">
                            ${item.nama_barang}
                            <div class="text-xs text-slate-400 font-normal">${item.jenis_barang || '-'}</div>
                        </td>
                        <td class="p-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold ${parseInt(item.kuantitas_stok) > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'}">
                                ${item.kuantitas_stok}
                            </span>
                        </td>
                        <td class="p-3 font-medium">${formatHarga}</td>
                        <td class="p-3">${item.nama_gudang || '-'}</td>
                        <td class="p-3">${item.nama_vendor || '-'}</td>
                        <td class="p-3 text-center">
                            <button onclick="handleDeleteBarang('${item.serial_number}')" class="text-red-500 hover:text-red-700 font-bold text-xs">Hapus</button>
                        </td>
                    </tr>
                `;
            });
        }

        renderAlertStok(stokHabisList);
    } catch (err) {
        console.error('Gagal memuat data barang:', err);
    }
}

function renderAlertStok(stokHabisList) {
    const container = document.getElementById('alertContainer');
    if (!container) return;

    if (stokHabisList.length > 0) {
        container.innerHTML = `
            <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-xl text-red-800 text-sm flex items-center justify-between shadow-sm">
                <div>
                    <strong class="font-bold">⚠️ ALERT (Stok Habis):</strong> 
                    Barang berikut memiliki stok 0: <span class="font-semibold">${stokHabisList.join(', ')}</span>. Harap segera hubungi vendor terkait!
                </div>
            </div>
        `;
    } else {
        container.innerHTML = '';
    }
}

async function handleTambahBarang(e) {
    e.preventDefault();
    const payload = {
        serial_number: document.getElementById('serial_number').value,
        nama_barang: document.getElementById('nama_barang').value,
        jenis_barang: document.getElementById('jenis').value,
        kuantitas_stok: parseInt(document.getElementById('stok').value),
        harga: parseFloat(document.getElementById('harga').value),
        id_gudang: document.getElementById('id_gudang').value || null,
        id_vendor: document.getElementById('id_vendor').value || null
    };

    try {
        const res = await fetch(API_BARANG, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const data = await res.json();
        if (res.ok) {
            alert('Barang berhasil ditambahkan!');
            document.getElementById('formBarang').reset();
            loadDataBarang();
            window.switchTab('stok');
        } else {
            alert(data.message || 'Gagal menambahkan barang.');
        }
    } catch (err) {
        alert('Terjadi kesalahan server.');
    }
}

async function handleTambahGudang(e) {
    e.preventDefault();
    const payload = {
        nama_gudang: document.getElementById('nama_gudang').value,
        lokasi: document.getElementById('lokasi').value
    };

    try {
        const res = await fetch(API_GUDANG, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        if (res.ok) {
            alert('Cabang gudang berhasil ditambahkan!');
            document.getElementById('formGudang').reset();
            loadDropdownsGudangVendor();
            window.switchTab('stok');
        } else {
            alert('Gagal menambahkan gudang.');
        }
    } catch (err) {
        alert('Terjadi kesalahan server.');
    }
}

async function handleTambahVendor(e) {
    e.preventDefault();
    const payload = {
        nama: document.getElementById('nama_vendor').value,
        kontak: document.getElementById('kontak_vendor').value
    };

    try {
        const res = await fetch(API_VENDOR, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        if (res.ok) {
            alert('Vendor berhasil ditambahkan!');
            document.getElementById('formVendor').reset();
            loadDropdownsGudangVendor();
            window.switchTab('stok');
        } else {
            alert('Gagal menambahkan vendor.');
        }
    } catch (err) {
        alert('Terjadi kesalahan server.');
    }
}

async function handleDeleteBarang(serialNumber) {
    if (!confirm(`Hapus barang dengan Serial Number: ${serialNumber}?`)) return;

    try {
        const res = await fetch(`${API_BARANG}?serial_number=${encodeURIComponent(serialNumber)}`, {
            method: 'DELETE'
        });

        if (res.ok) {
            loadDataBarang();
        } else {
            alert('Gagal menghapus barang.');
        }
    } catch (err) {
        alert('Terjadi kesalahan server saat menghapus.');
    }
}