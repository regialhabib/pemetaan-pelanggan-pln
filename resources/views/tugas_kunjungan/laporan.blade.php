@extends('layouts.app')

@push('style')
    <!-- DataTables -->
    <link href="{{ URL::asset('libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="{{ URL::asset('libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />

    <!-- Sweet Alert-->
    <link href="{{ asset('libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush
@section('main-content')


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-block-helper me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                <i class="mdi mdi-block-helper me-2"></i>
                                {{ $error }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endforeach
                    @endif


                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Filter Tanggal</h6>
                        </div>

                        <div class="card-body">
                            <form id="formFilter">
                                <div class="row align-items-end">

                                    <!-- Tanggal Awal -->
                                    <div class="col-md-4">
                                        <label>Tanggal Awal</label>
                                        <input type="date" id="tanggal_awal" class="form-control" required>
                                    </div>

                                    <!-- Tanggal Akhir -->
                                    <div class="col-md-4">
                                        <label>Tanggal Akhir</label>
                                        <input type="date" id="tanggal_akhir" class="form-control" required>
                                    </div>

                                    <!-- Tombol -->
                                    <div class="col-md-4 d-flex gap-2">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i> Tampilkan
                                        </button>

                                        <a href="#" id="btnPrint" class="btn btn-danger d-none" target="_blank">
                                            <i class="fas fa-file-pdf"></i> Print PDF
                                        </a>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>

                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>no</th>
                                <th>Tanggal Kunjungan </th>
                                <th>Nama Petugas</th>
                                <th>Nama Pelanggan</th>
                                <th>ID Pelanggan</th>
                                <th>Alamat</th>
                                <th>Keterangan</th>

                            </tr>
                        </thead>


                    </table>

                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->


    <!--  Form Delete -->
    <form id="form-delete" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>


@endsection

@push('script')
    <!-- Required datatable js -->
    <script src="{{ URL::asset('libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Datatable init js -->


    <!-- Sweet Alerts js -->
    <script src="{{ asset('libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        function formatTanggalWaktu(tanggalISO) {

            if (!tanggalISO) return '-';

            let date = new Date(tanggalISO);

            let options = {
                day: '2-digit',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                timeZone: 'Asia/Jakarta'
            };

            return date.toLocaleString('id-ID', options).replace('pukul', '');
        }


        let table;

        $(document).ready(function() {

            table = $('#datatable').DataTable({
                processing: true,
                serverSide: false,
                searching: false,
                ordering: false,
                paging: true,
                info: true,
                language: {
                    emptyTable: "Silakan pilih rentang tanggal"
                },
                columns: [{
                        data: null
                    },
                    {
                        data: 'tugas.created_at'
                    },
                    {
                        data: 'tugas.petugas.nama'
                    },
                    {
                        data: 'pelanggan.nama'
                    },
                    {
                        data: 'pelanggan.id_pelanggan'
                    },
                    {
                        data: 'pelanggan.alamat'
                    },
                    {
                        data: 'tugas.keterangan'
                    },


                ],
                columnDefs: [{
                        targets: 0,
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        targets: 1,
                        render: data => formatTanggalWaktu(data)
                    }
                ]
            });

            $('#formFilter').on('submit', function(e) {
                e.preventDefault();

                let awal = $('#tanggal_awal').val();
                let akhir = $('#tanggal_akhir').val();

                $.ajax({
                    url: "{{ route('laporan.data') }}",
                    type: "GET",
                    data: {
                        tanggal_awal: awal,
                        tanggal_akhir: akhir
                    },
                    success: function(res) {

                        table.clear().rows.add(res.data).draw();
                        console.log(res.data);


                        $('#btnPrint')
                            .removeClass('d-none')
                            .attr('href',
                                "{{ route('laporan.print') }}?tanggal_awal=" +
                                awal + "&tanggal_akhir=" + akhir
                            );
                    }
                });
            });

        });
    </script>
@endpush
