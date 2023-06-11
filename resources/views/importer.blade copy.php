<form action="{{ route('excel.import') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label for="excel_file" class="form-label">Excel File</label>
        <input type="file" class="form-control" id="excel_file" name="excel_file">
        @error('excel_file')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <button type="submit" class="btn btn-primary">Import</button>
</form>
