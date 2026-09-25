<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Kunjungan</title>

    <link rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
</head>

<body>

    <!-- HEADER PLN -->
    <div class="header">
        <h4>PT PLN (Persero)</h4>
        <p>UNIT LAYANAN PELANGGAN TELANAIPURA</p>
        <p>Telanaipura, Kota Jambi</p>
    </div>

    <div class="garis"></div>

    <!-- JUDUL LAPORAN -->
    <div class="judul">
        <b>LAPORAN HASIL KUNJUNGAN PELANGGAN</b><br>
        Periode:
        {{ \Carbon\Carbon::parse($tanggal_awal)->format('d-m-Y') }}
        s/d
        {{ \Carbon\Carbon::parse($tanggal_akhir)->format('d-m-Y') }}
    </div>

    <!-- TABEL LAPORAN -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Kunjungan</th>
                <th>Nama Petugas</th>
                <th>Nama Pelanggan</th>
                <th>ID Pelanggan</th>
                <th>Alamat</th>
                <th>Keterangan</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($data as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>

                    <td>
                        {{ \Carbon\Carbon::parse(optional($item->tugas)->created_at)->format('d-m-Y H:i') }}
                    </td>

                    <td>
                        {{ optional(optional($item->tugas)->petugas)->nama }}
                    </td>

                    <td>
                        {{ optional($item->pelanggan)->nama }}
                    </td>

                    <td>
                        {{ optional($item->pelanggan)->id_pelanggan }}
                    </td>

                    <td>
                        {{ optional($item->pelanggan)->alamat }}
                    </td>

                    <td>
                        {{ optional($item->tugas)->keterangan }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
