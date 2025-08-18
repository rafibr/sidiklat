// shared.js
// Utility and data loader for SPA
window.pelatihanData = [];
window.employeeData = [];

async function loadPelatihanData() {
	try {
		const response = await fetch('../data-pelatihan.json');
		window.pelatihanData = await response.json();
	} catch (e) {
		window.pelatihanData = [];
	}
}

async function loadEmployeeData() {
	try {
		const response = await fetch('../data-pegawai.json');
		const data = await response.json();
		window.employeeData = data.employees || [];
	} catch (e) {
		window.employeeData = [];
	}
}

function showNotification(msg, type = 'info') {
	alert(msg); // Simple fallback
}

function formatPercentageChange(val) {
	if (isNaN(val) || !isFinite(val)) return '0%';
	const sign = val >= 0 ? '+' : '';
	return `${sign}${val.toFixed(1)}%`;
}

function getBadgeClass(jenis) {
	const badges = {
		'Diklat Struktural': 'bg-blue-100 text-blue-800',
		'Diklat Fungsional': 'bg-green-100 text-green-800',
		'Diklat Teknis': 'bg-purple-100 text-purple-800',
		'Workshop': 'bg-orange-100 text-orange-800',
		'Seminar': 'bg-red-100 text-red-800',
		'Pelatihan Jarak Jauh': 'bg-teal-100 text-teal-800',
		'E-Learning': 'bg-indigo-100 text-indigo-800',
	};
	return badges[jenis] || 'bg-gray-100 text-gray-800';
}
