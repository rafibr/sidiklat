graph LR
User[User]
Admin[Admin]

Login[Login]
ViewDashboard[View Dashboard]
ViewPelatihan[View Pelatihan]
DownloadSertifikat[Download Sertifikat]
ViewProgress[View Progress]
FilterProgress[Filter Progress]
CompareTahunan[Compare Tahunan]
GenerateReport[Generate Report]
Logout[Logout]

ManagePelatihan[Manage Pelatihan]
UploadSertifikat[Upload Sertifikat]
ManageUsers[Manage Users]

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
Admin --> GenerateReport

ManagePelatihan -.-> UploadSertifikat
CompareTahunan -.-> GenerateReport
