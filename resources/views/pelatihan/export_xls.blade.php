<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Export Pelatihan</title>
	<style>
		table {
			border-collapse: collapse;
			width: 100%;
		}

		th,
		td {
			border: 1px solid #000;
			padding: 8px;
			text-align: left;
			vertical-align: top;
		}

		th {
			background-color: #f2f2f2;
			font-weight: bold;
		}

		.date-cell {
			mso-number-format: "yyyy-mm-dd";
		}
	</style>
</head>

<body>
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
					<br />{{ optional($it->pegawai)->nip }}
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
				<td class="date-cell">{{ $it->tanggal_mulai ? date('Y-m-d', strtotime($it->tanggal_mulai)) : '' }}</td>
				<td class="date-cell">{{ $it->tanggal_selesai ? date('Y-m-d', strtotime($it->tanggal_selesai)) : '' }}
				</td>
				<td>{{ $it->jp }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</body>

</html>