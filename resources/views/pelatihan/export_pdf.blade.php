<!doctype html>
<html>

<head>
	<meta charset="utf-8">
	<title>Export Pelatihan</title>
	<style>
		body {
			font-family: DejaVu Sans, sans-serif;
			font-size: 12px;
		}

		table {
			width: 100%;
			border-collapse: collapse;
		}

		th,
		td {
			border: 1px solid #ddd;
			padding: 6px;
		}

		th {
			background: #f3f4f6;
		}
	</style>
</head>

<body>
	<h2>Data Pelatihan</h2>
	<table>
		<thead>
			<tr>
				<th>ID</th>
				<th>Pegawai</th>
				<th>Nama Pelatihan</th>
				<th>Jenis</th>
				<th>Penyelenggara</th>
				<th>Tanggal Mulai</th>
				<th>Tanggal Selesai</th>
				<th>JP</th>
			</tr>
		</thead>
		<tbody>
			@foreach($items as $it)
			<tr>
				<td>{{ $it->id }}</td>
				<td>
					{{ optional($it->pegawai)->nama_lengkap }}
					@if(optional($it->pegawai)->nip)
					<br><small>{{ optional($it->pegawai)->nip }}</small>
					@endif
				</td>
				<td>{{ $it->nama_pelatihan }}</td>
				<td>
					@if($it->jenisPelatihan)
					{{ $it->jenisPelatihan->nama }}
					@else
					{{ $it->getJenisNama() }}
					@endif
				</td>
				<td>{{ $it->penyelenggara }}</td>
				<td>{{ $it->tanggal_mulai ? date('Y-m-d', strtotime($it->tanggal_mulai)) : '' }}</td>
				<td>{{ $it->tanggal_selesai ? date('Y-m-d', strtotime($it->tanggal_selesai)) : '' }}</td>
				<td>{{ $it->jp }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</body>

</html>