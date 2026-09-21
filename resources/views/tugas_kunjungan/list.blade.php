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


                    <div class="d-flex justify-content-between items-center mb-4  align-items-center">
                        <h4 class="card-title"> Daftar Tugas Kunjungan</h4>

                    </div>
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>No </th>
                                <th>Keterangan</th>
                                <th>Nama Petugas</th>
                                <th>Status</th>
                                <th>Aksi</th>

                            </tr>
                        </thead>

                        @php
                            $no = 1;
                        @endphp

                        <tbody>
                            @foreach ($tugasKunjungans as $tugasKunjungan)
                                <tr>
                                    <td>
                                        {{ $no++ }}
                                    </td>

                                    <td>{{ $tugasKunjungan->keterangan }}</td>
                                    <td>{{ $tugasKunjungan->petugas->nama }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $tugasKunjungan->status_tugas == 'selesai' ? 'bg-success' : 'bg-warning' }}">
                                            {{ format_status($tugasKunjungan->status_tugas) }}
                                        </span>
                                    </td>
                                    <td><a href="{{ route('tugas_kunjungan.detail', $tugasKunjungan->id) }}"
                                            class="btn btn-sm btn-primary">Detail</a>
                                        @can('admin')
                                            <a href="javascript:void(0)" class="btn btn-sm btn-danger btn-delete"
                                                data-url="{{ route('tugas_kunjungan.destroy', $tugasKunjungan->id) }}">
                                                <i class="bx bx-trash"></i>
                                            </a>
                                            <a href="{{ route('tugas_kunjungan.map', $tugasKunjungan->id) }}" class="btn btn-sm btn-info">
                                                <i class="far fa-map"> Lihat Peta</i>
                                            </a>
                                        @endcan

                                    </td>
                                </tr>
                            @endforeach


                        </tbody>
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
    <script src="{{ URL::asset('js/pages/datatables.init.js') }}"></script>

    <!-- Sweet Alerts js -->
    <script src="{{ asset('libs/sweetalert2/sweetalert2.min.js') }}"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}'
            });
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-delete');
            const form = document.getElementById('form-delete');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const url = this.getAttribute('data-url');

                    Swal.fire({
                        title: 'Yakin ingin menghapus?',
                        text: 'Data  yang dihapus tidak dapat dikembalikan!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.action = url;
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>


@endpush
