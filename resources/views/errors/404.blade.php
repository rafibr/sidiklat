<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman tidak ditemukan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="relative flex min-h-screen items-center justify-center px-4 py-16 sm:px-6 lg:px-8">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div class="absolute inset-x-0 top-[-280px] h-[500px] bg-gradient-to-b from-indigo-500/20 via-slate-900/60 to-transparent"></div>
            <div class="absolute left-[-160px] bottom-[-80px] h-96 w-96 rounded-full bg-indigo-400/10 blur-3xl"></div>
            <div class="absolute right-[-160px] top-[120px] h-80 w-80 rounded-full bg-emerald-400/10 blur-3xl"></div>
        </div>
        <div class="relative w-full max-w-2xl overflow-hidden rounded-3xl border border-white/10 bg-white/5 p-10 text-center shadow-2xl backdrop-blur-xl">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-white/10 text-white">
                <i class="fas fa-compass"></i>
            </div>
            <h1 class="mt-6 text-3xl font-semibold tracking-tight text-white sm:text-4xl">Halaman tidak ditemukan</h1>
            <p class="mt-4 text-sm text-slate-300">Kami tidak dapat menemukan halaman yang Anda minta. Gunakan navigasi di bawah untuk kembali memantau data pelatihan dan progres JP pegawai.</p>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-200">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Dashboard
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-white/30 px-6 py-3 text-sm font-medium text-white transition hover:border-white/60 hover:bg-white/10">
                    <i class="fas fa-right-to-bracket"></i>
                    Masuk ulang
                </a>
            </div>
            <div class="mt-10 grid gap-4 rounded-2xl border border-white/10 bg-white/5 p-6 text-left text-sm text-slate-300 sm:grid-cols-3">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-400">Monitoring Pegawai</p>
                    <p class="mt-1 text-slate-200">Pantau JP pegawai dan status pencapaiannya.</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-400">Data Pelatihan</p>
                    <p class="mt-1 text-slate-200">Kelola riwayat pelatihan beserta sertifikat.</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-400">Analitik Tahunan</p>
                    <p class="mt-1 text-slate-200">Lihat ringkasan kinerja berdasarkan tahun pelaporan.</p>
                </div>
            </div>
        </div>
        <p class="absolute bottom-6 text-xs text-slate-500">SIDIKLAT • Sistem Informasi Diklat ASN</p>
    </div>
</body>

</html>
