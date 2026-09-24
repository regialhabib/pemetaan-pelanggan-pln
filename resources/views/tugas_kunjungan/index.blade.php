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
                        <h4 class="card-title mb-0">Pilih Pelanggan untuk Ditugaskan</h4>

                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-success pe-none fw-medium">
                                <span id="jumlahDipilih">0</span> Pelanggan Dipilih
                            </button>
                            <button type="button" class="btn btn-sm btn-primary shadow-sm d-inline-flex align-items-center fw-medium" onclick="openAddModal()">
                                Lanjutkan Penugasan <i class="bx bx-right-arrow-alt font-size-16 ms-1"></i>
                            </button>
                        </div>

                    </div>
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>Pilih </th>
                                <th>IDPEl</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Golongan Tarif</th>
                                <th>Daya</th>
                            </tr>
                        </thead>

                        @php
                            $no = 1;
                        @endphp

                        <tbody>
                            @foreach ($pelanggans as $pelanggan)
                                <tr>
                                    <td><input type="checkbox" class="checkbox-pelanggan" value="{{ $pelanggan->id }}"
                                            onchange="handleCheckbox(this)">

                                    </td>

                                    <td>{{ $pelanggan->id_pelanggan }}</td>
                                    <td>{{ $pelanggan->nama }}</td>
                                    <td>{{ $pelanggan->alamat }}</td>
                                    <td>{{ $pelanggan->latitude }}</td>
                                    <td>{{ $pelanggan->longitude }}</td>
                                    <td>{{ $pelanggan->golongan_tarif }}</td>
                                    <td>{{ $pelanggan->daya }}</td>


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


    <!--  Modal Add -->
    <div class="modal fade" id="addModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog"
        aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Buat Tugas Kunjungan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('tugas_kunjungan.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="pelanggan" id="pelanggan">
                        <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center">
                            <i class="bx bx-info-circle font-size-18 me-2"></i>
                            <span>Anda akan menugaskan <strong id="jumlah_pelanggan_text">0</strong> pelanggan.</span>
                        </div>
                        <input type="hidden" class="form-control" disabled id="jumlah_pelanggan">
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="keterangan" class="form-label">Keterangan / Instruksi Tugas</label>
                                <textarea name="keterangan" class="form-control" id="keterangan" cols="30" rows="3" placeholder="Contoh: Lakukan pengecekan meteran..."></textarea>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label for="petugas" class="form-label">Petugas</label>
                                <select name="petugas" id="petugas" class="form-select">
                                    @foreach ($petugass as $petugas)
                                        <option value="{{ $petugas->id }}">{{ $petugas->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
                </form>
            </div>
        </div>
    </div>




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
        let pelangganDipilih = [];

        const checkAll = document.getElementById('checkAll');
        const checkboxes = document.querySelectorAll('.checkbox-pelanggan');


        const jumlahDipilih = document.getElementById('jumlahDipilih');

        function handleCheckbox(el) {

            let id = el.value;

            if (el.checked) {

                // Tambah ke array jika belum ada
                if (!pelangganDipilih.includes(id)) {
                    pelangganDipilih.push(id);
                }

            } else {

                // Hapus jika di-uncheck
                pelangganDipilih = pelangganDipilih.filter(function(item) {
                    return item != id;
                });

            }
            console.table(pelangganDipilih);

            jumlahDipilih.innerHTML = pelangganDipilih.length;
            document.getElementById('jumlah_pelanggan').value = pelangganDipilih.length;
            const textEl = document.getElementById('jumlah_pelanggan_text');
            if(textEl) textEl.innerHTML = pelangganDipilih.length;
            
            const jsonString = JSON.stringify(pelangganDipilih);
            document.getElementById('pelanggan').value = jsonString;

        }
        
        function openAddModal() {
            if (pelangganDipilih.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Pelanggan',
                    text: 'Silakan centang minimal 1 pelanggan pada tabel.'
                });
                return;
            }
            var myModal = new bootstrap.Modal(document.getElementById('addModal'));
            myModal.show();
        }
    </script>



@endpush
