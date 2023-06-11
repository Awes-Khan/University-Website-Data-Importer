    <head>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ 'Import Excel Data' }}
        </h2>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="/library/mdbootstrap/css/mdb.min.css"/>
        <script src="{{ asset('/library/mdbootstrap/js/mdb.min.js') }}"></script>
        <link rel="stylesheet" href="{{ asset('/library/sweetalert2/dist/sweetalert2.min.js') }}"/>
        <script src="{{ asset('/library/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <head>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="container">
                        <div id="file_upload">

                        <style>
                            body {
                        margin: 20px 60px;
                        }

                        .drop-container {
                        position: relative;
                        display: flex;
                        gap: 10px;
                        flex-direction: column;
                        justify-content: center;
                        align-items: center;
                        height: 200px;
                        padding: 20px;
                        border-radius: 10px;
                        border: 2px dashed #555;
                        color: #444;
                        cursor: pointer;
                        transition: background .2s ease-in-out, border .2s ease-in-out;
                        }

                        .drop-container:hover {
                        background: #eee;
                        border-color: #111;
                        }

                        .drop-container:hover .drop-title {
                        color: #222;
                        }

                        .drop-title {
                        color: #444;
                        font-size: 20px;
                        font-weight: bold;
                        text-align: center;
                        transition: color .2s ease-in-out;
                        }

                        input[type=file] {
                        width: 350px;
                        max-width: 100%;
                        color: #444;
                        padding: 5px;
                        background: #fff;
                        border-radius: 10px;
                        border: 1px solid #555;
                        }

                        input[type=file]::file-selector-button {
                        margin-right: 20px;
                        border: none;
                        background: #084cdf;
                        padding: 10px 20px;
                        border-radius: 10px;
                        color: #fff;
                        cursor: pointer;
                        transition: background .2s ease-in-out;
                        }

                        input[type=file]::file-selector-button:hover {
                        background: #0d45a5;
                        }
                        </style>
                        @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                        @endif
                        @if(isset($errors) && $errors->any())
                        @foreach ($errors->all() as $error)

                        <div class="alert alert-danger">
                            {{ $error}}
                        </div>
                        @endforeach

                        @endif
                        <form action="{{ route('excel.import') }}"
                                method="POST"
                                enctype="multipart/form-data">
                              @csrf
                              {{-- <input type="file" name="file" --}}
                                     {{-- class="form-control"> --}}

                        <label for="images" class="drop-container">
                            <span class="drop-title">Drop files here</span>
                            or
                            <input type="file" id="images" name="file" accept=".csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                            @error('excel_file')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                            @if(session()->has('message'))
                                <div class="alert alert-success">
                                    {{ session()->get('message') }}
                                </div>
                            @endif
                        </label>
                        <br>
                        <div class="row">
                        <button class="col-sm-12 btn btn-success">
                              Import 
                        </button>
                           <div class="col-sm"></div>
                        {{-- <a class="col-sm-2 btn btn-warning"
                           href="{{ route('export.blank') }}">
                                  Download Template
                          </a> --}}
                        </div>
                    </form>




                        </div>
                        {{-- <div class="card bg-light mt-3">
                            <div class="card-header">
                            </div>
                            <div class="card-body">
                                <form action="{{ route('import') }}"
                                      method="POST"
                                      enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" name="file"
                                           class="form-control">
                                    <br>
                                    <button class="btn btn-success">
                                          Import User Data
                                       </button>
                                    <a class="btn btn-warning"
                                       href="{{ route('export') }}">
                                              Export User Data
                                      </a>
                                </form>
                            </div>
                            <div class="datatable" data-mdb-full-pagination="true" data-mdb-selectable="true" data-mdb-multi="true" data-mdb-fixed-header="true" data-mdb-loading="true"></div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
