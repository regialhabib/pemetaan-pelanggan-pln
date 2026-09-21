<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Kunjungan</title>

    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        .header {
            text-align: center;
            line-height: 1.5;
        }

        .header h4 {
            margin: 0;
            font-weight: bold;
        }

        .header p {
            margin: 0;
        }

        .garis {
            border-top: 2px solid black;
            border-bottom: 1px solid black;
            margin-top: 5px;
            margin-bottom: 15px;
        }

        .judul {
            text-align: center;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
        }

        th {
            background-color: #f2f2f2;
        }

        .center {
            text-align: center;
        }
    </style>
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
