
graph LR
    User[User]
    Admin[Admin]

    subgraph UserFeatures[Kebutuhan User]
        Login[Login]
        ViewDashboard[Lihat Dashboard]
        ViewPelatihan[Lihat Data Pelatihan]
        DownloadSertifikat[Unduh Sertifikat]
        ViewProgress[Lihat Progress]
        FilterProgress[Filter Progress]
        CompareTahunan[Bandingkan Progress Tahunan]
        GenerateReport[Buat Laporan]
        Logout[Logout]
    end

    subgraph AdminFeatures[Kebutuhan Admin]
        ManagePelatihan[Kelola Pelatihan]
        UploadSertifikat[Unggah Sertifikat]
        ManageUsers[Kelola Pengguna]
        GenerateReportAdmin[Buat Laporan]
    end

    User --> Login
    User --> ViewDashboard
    User --> ViewPelatihan
    User --> DownloadSertifikat
    User --> ViewProgress
    User --> FilterProgress
    User --> CompareTahunan
    User --> GenerateReport
    User --> Logout

    Admin --> ManagePelatihan
    Admin --> UploadSertifikat
    Admin --> ManageUsers
    Admin --> GenerateReportAdmin
