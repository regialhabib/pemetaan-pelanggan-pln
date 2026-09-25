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

    <div class="row ">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="importForm" enctype="multipart/form-data" onsubmit="event.preventDefault(); startImport();">
                        @csrf


                        <!-- TITLE -->
                        <h5 class="mb-2">Import Data Pelanggan</h5>
                        <p class="text-muted mb-4">
                            Upload file Excel untuk mengimpor data pelanggan
                        </p>

                        <!-- DROP ZONE -->
                        <div class="drop-zone mb-3" id="dropZone">
                            <p class="fw-bold mb-1">Drag & Drop file Excel di sini</p>
                            <small>.xls, .xlsx, .csv</small>
                            <input type="file" name="file" id="fileInput" hidden accept=".xls,.xlsx,.csv">
                        </div>

                        <!-- PREVIEW -->
                        <div id="preview" class="mb-3"></div>

                        <!-- ACTION -->
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-dark btn-sm" id="btnImport" disabled>
                                <i class="bx bx-import"></i> Import
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

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
                        <h4 class="card-title">Data Pelanggan</h4>

                        <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#addModal"><i
                                class="bx bx-add-to-queue "></i> Tambah Pelanggan</button>
                    </div>
                    <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>No </th>
                                <th>IDPEl</th>
                                <th>Nama</th>
                                <th>No HP</th>
                                <th>Alamat</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Golongan Tarif</th>
                                <th>Daya</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        @php
                            $no = 1;
                        @endphp

                        <tbody>
                            @foreach ($pelanggans as $pelanggan)
                                <tr>
                                    <td>
                                        {{ $no++ }}
                                        
                                    </td>

                                    <td>{{ $pelanggan->id_pelanggan }}</td>
                                    <td>{{ $pelanggan->nama }}</td>
                                    <td>{{ $pelanggan->no_hp }}</td>
                                    <td>{{ $pelanggan->alamat }}</td>
                                    <td>{{ $pelanggan->latitude }}</td>
                                    <td>{{ $pelanggan->longitude }}</td>
                                    <td>{{ $pelanggan->golongan_tarif }}</td>
                                    <td>{{ $pelanggan->daya }}</td>

                                    <td>
                                        <ul class="list-unstyled hstack gap-1 mb-0">
                                            <button class="btn btn-sm btn-primary btn-edit" data-bs-toggle="modal"
                                                data-bs-target="#staticBackdrop" data-id="{{ $pelanggan->id }}"
                                                data-nama="{{ $pelanggan->nama }}" data-no_hp="{{ $pelanggan->no_hp }}" data-alamat="{{ $pelanggan->alamat }}"
                                                data-latitude="{{ $pelanggan->latitude }}"
                                                data-longitude="{{ $pelanggan->longitude }}"
                                                data-daya="{{ $pelanggan->daya }}"
                                                data-golongan_tarif="{{ $pelanggan->golongan_tarif }}"><i
                                                    class="bx bx-edit"></i></button>
                                            <a href="javascript:void(0)" class="btn btn-sm btn-danger btn-delete"
                                                data-url="{{ route('pelanggan.destroy', $pelanggan->id) }}">
                                                <i class="bx bx-trash"></i>
                                            </a>
                                        </ul>
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


    <!--  Modal Add -->
    <div class="modal fade" id="addModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog"
        aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Tambah Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('pelanggan.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <label for="id_pelanggan" class="form-label">ID Pelanggan</label>
                                <input type="text" class="form-control" id="id_pelanggan" name="id_pelanggan">
                            </div>
                            <div class="col-md-4">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama">
                            </div>
                            <div class="col-md-4">
                                <label for="no_hp" class="form-label">No HP</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mt-2">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea id="textarea" class="form-control" maxlength="225" rows="3" placeholder="" name="alamat"></textarea>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="golongan_tarif" class="form-label">Golongan Tarif</label>
                                <input type="text" class="form-control" id="golongan_tarif" name="golongan_tarif">
                            </div>
                            <div class="col-md-6">
                                <label for="daya" class="form-label">Daya</label>
                                <input type="text" class="form-control" id="daya" name="daya">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="text" class="form-control" id="latitude" name="latitude">
                            </div>
                            <div class="col-md-6">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="text" class="form-control" id="longitude" name="longitude">
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

    <!--  Modal Edit -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Edit Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('pelanggan.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" id="idEdit">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="namaEdit" name="nama">
                            </div>
                            <div class="col-md-6">
                                <label for="no_hp" class="form-label">No HP</label>
                                <input type="text" class="form-control" id="no_hpEdit" name="no_hp">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <label for="alamat" class="form-label">Alamat</label>
                                <input type="text" class="form-control" id="alamatEdit" name="alamat">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="golongan_tarif" class="form-label">Golongan Tarif</label>
                                <input type="text" class="form-control" id="golongan_tarifEdit"
                                    name="golongan_tarif">
                            </div>
                            <div class="col-md-6">
                                <label for="daya" class="form-label">Daya</label>
                                <input type="text" class="form-control" id="dayaEdit" name="daya">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="text" class="form-control" id="latitudeEdit" name="latitude">
                            </div>
                            <div class="col-md-6">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="text" class="form-control" id="longitudeEdit" name="longitude">
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div id="importOverlay" class="import-overlay d-none">
        <div class="overlay-card text-center">
            <div class="spinner-border text-primary mb-3" role="status"></div>

            <div class="progress mb-2" style="height: 18px;">
                <div id="overlayProgressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                    style="width: 0%">
                </div>
            </div>

            <small id="overlayProgressText" class="text-muted">
                Mengimpor data pelanggan...
            </small>
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
        const btnEdit = document.querySelectorAll('.btn-edit');
        btnEdit.forEach((btn) => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                const nama = btn.getAttribute('data-nama');
                const no_hp = btn.getAttribute('data-no_hp');
                const alamat = btn.getAttribute('data-alamat');
                const latitude = btn.getAttribute('data-latitude');
                const longitude = btn.getAttribute('data-longitude');
                const daya = btn.getAttribute('data-daya');
                const golongan_tarif = btn.getAttribute('data-golongan_tarif');

                document.getElementById('idEdit').value = id;
                document.getElementById('namaEdit').value = nama;
                document.getElementById('no_hpEdit').value = no_hp || '';
                document.getElementById('alamatEdit').value = alamat;
                document.getElementById('latitudeEdit').value = latitude;
                document.getElementById('longitudeEdit').value = longitude;
                document.getElementById('dayaEdit').value = daya;
                document.getElementById('golongan_tarifEdit').value = golongan_tarif;
            })
        })
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-delete');
            const form = document.getElementById('form-delete');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const url = this.getAttribute('data-url');

                    Swal.fire({
                        title: 'Yakin ingin menghapus?',
                        text: 'Data pelanggan yang dihapus tidak dapat dikembalikan!',
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
  

    <script>
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const preview = document.getElementById('preview');
        const btnImport = document.getElementById('btnImport');

        const allowedTypes = [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/csv'
        ];

        dropZone.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', () => handleFile(fileInput.files[0]));

        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('dragover');

            const file = e.dataTransfer.files[0];
            fileInput.files = e.dataTransfer.files;
            handleFile(file);
        });

        function handleFile(file) {
            preview.innerHTML = '';
            btnImport.disabled = true;

            if (!file) return;

            if (!allowedTypes.includes(file.type)) {
                preview.innerHTML = `
                <div class="alert alert-danger">
                    File tidak valid. Hanya Excel (.xls, .xlsx, .csv)
                </div>`;
                fileInput.value = '';
                return;
            }

            preview.innerHTML = `
            <div class="file-preview">
                <button class="remove-file" onclick="removeFile()">
                    <i class="bx bx-x"></i>
                </button>
                <i class="bx bx-file"></i>
                <div>
                    <div class="fw-semibold">${file.name}</div>
                    <small class="text-muted">
                        ${(file.size / 1024).toFixed(2)} KB
                    </small>
                </div>
            </div>
        `;

            btnImport.disabled = false;
        }

        function removeFile() {
            fileInput.value = '';
            preview.innerHTML = '';
            btnImport.disabled = true;
        }

        function showImportOverlay() {
            document.getElementById('importOverlay').classList.remove('d-none');
        }

        function hideImportOverlay() {
            document.getElementById('importOverlay').classList.add('d-none');
        }


        function startImport() {
            const BASE_URL = document
                .querySelector('meta[name="base-url"]')
                .getAttribute('content');

            const overlay = document.getElementById('importOverlay');
            const bar = document.getElementById('overlayProgressBar');
            const text = document.getElementById('overlayProgressText');

            let finished = false;

            // reset
            bar.style.width = '0%';
            bar.innerText = '0%';
            text.innerText = 'Menyiapkan import...';

            overlay.classList.remove('d-none');

            fetch(`${BASE_URL}/pelanggan/import`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: new FormData(document.getElementById('importForm'))
                })
                .then(res => {
                    if (!res.ok) throw new Error('Gagal memulai import');
                    return res.json();
                })
                .then(({
                    key
                }) => {

                    const interval = setInterval(() => {
                        fetch(`${BASE_URL}/pelanggan/import/progress/${key}`)
                            .then(res => {
                                if (!res.ok) throw new Error('Gagal membaca progress');
                                return res.json();
                            })
                            .then(data => {

                                bar.style.width = data.progress + '%';
                                bar.innerText = data.progress + '%';
                                text.innerText = `Mengimpor data... ${data.progress}%`;

                                if (data.progress >= 100 && !finished) {
                                    finished = true;
                                    clearInterval(interval);

                                    overlay.classList.add('d-none');
                                    removeFile();
                                    location.reload();
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Import Berhasil',
                                        text: 'Data pelanggan berhasil diimpor',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            })
                            .catch(err => {
                                if (finished) return;
                                finished = true;
                                clearInterval(interval);

                                overlay.classList.add('d-none');

                                removeFile();

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Terjadi kesalahan saat membaca progress import'
                                });
                            });

                    }, 700);
                })
                .catch(err => {
                    if (finished) return;
                    finished = true;

                    overlay.classList.add('d-none');

                    console.error(err);

                    Swal.fire({
                        icon: 'error',
                        title: 'Import Gagal',
                        text: 'Gagal memulai proses import'
                    });
                });
        }
    </script>


@endpush
