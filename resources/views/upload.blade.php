<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<div class="container mt-3">
    <form action="{{ route('summarize') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3 mt-3">
            <input require class="form-control" type="file" name="pdf" accept=".pdf">
        </div>

        <div class="mb-3 mt-3">
            <label for="name" class="form-label">Question:</label>
            <input require type="text" class="form-control"  placeholder="Enter Question" name="question">
        </div>

        <div class="mb-3 mt-3">
            <button class="btn btn-primary" type="submit">Summarize PDF</button>
        </div>
    </form>
</div>