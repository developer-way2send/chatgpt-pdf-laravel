<form action="{{ route('uploadPDF') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="pdf" accept=".pdf">
    <button type="submit">Summarize PDF</button>
</form>