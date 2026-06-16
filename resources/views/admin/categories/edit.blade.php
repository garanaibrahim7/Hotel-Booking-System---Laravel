@extends('layouts.adminlte')

@section('title', 'Edit Room Category')

@section('content')
<div class="container-fluid py-5">
    <div class="card border-0 shadow-sm m-auto" style="max-width: 850px; border-radius: 15px;">
        <div class="card-header bg-white border-0 pt-4 px-4 text-center">
            <h3 class="fw-bold text-dark mb-0">Edit Room Category</h3>
            <p class="text-muted small">Property: <span class="text-primary fw-semibold">{{ $category->hotel->name }}</span></p>
        </div>

        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            {{-- Container for IDs of images to be deleted from the database --}}
            <div id="deletedImagesContainer"></div>

            <div class="card-body px-4">
                <div class="row g-4">
                    
                    <div class="col-md-12">
                        <label class="form-label fw-semibold small text-uppercase">Hotel</label>
                        <input type="text" class="form-control bg-light border-0 py-2" value="{{ $category->hotel->name }}" readonly style="cursor: not-allowed">
                        <input type="hidden" name="hotel_id" value="{{ $category->hotel_id }}">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold small text-uppercase">Room Type</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($types as $type)
                                <input type="radio" class="btn-check" name="type" id="type_{{ $type }}" 
                                    value="{{ $type }}" {{ old('type', $category->type) == $type ? 'checked' : '' }}>
                                <label class="btn btn-outline-light text-dark border-0 bg-light px-4 py-2" for="type_{{ $type }}">
                                    {{ Str::title($type) }}
                                </label>
                            @endforeach
                        </div>
                        @error('type') <div class="text-danger extra-small mt-1" style="font-size: 0.8rem;">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold small text-uppercase">Category Level</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($categories as $cat)
                                <input type="radio" class="btn-check" name="category" id="cat_{{ $cat }}" 
                                    value="{{ $cat }}" {{ old('category', $category->category) == $cat ? 'checked' : '' }}>
                                <label class="btn btn-outline-light text-dark border-0 bg-light px-4 py-2" for="cat_{{ $cat }}">
                                    {{ Str::title($cat) }}
                                </label>
                            @endforeach
                        </div>
                        @error('category') <div class="text-danger extra-small mt-1" style="font-size: 0.8rem;">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold small text-uppercase">Description</label>
                        <textarea class="form-control bg-light border-0 @error('description') is-invalid @enderror" 
                                  name="description" placeholder="Describe the room features..." rows="3">{{ old('description', $category->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-uppercase">Allowed Guests</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-person"></i></span>
                            <input type="number" name="max_adults" class="form-control bg-light border-0 me-2 @error('max_adults') is-invalid @enderror" 
                                   value="{{ old('max_adults', $category->max_adults) }}" placeholder="Adults">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-person-arms-up"></i></span>
                            <input type="number" name="max_children" class="form-control bg-light border-0 @error('max_children') is-invalid @enderror" 
                                   value="{{ old('max_children', $category->max_children) }}" placeholder="Kids">
                        </div>
                        @error('max_adults') <div class="text-danger extra-small mt-1" style="font-size: 0.8rem;">{{ $message }}</div> @enderror
                        @error('max_children') <div class="text-danger extra-small mt-1" style="font-size: 0.8rem;">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-uppercase">Rent / Night ({{ $category->hotel->currency_code }})</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-tag"></i></span>
                            <input type="number" class="form-control bg-light border-0 @error('price') is-invalid @enderror" 
                                   name="price" value="{{ old('price', $category->price) }}" placeholder="0.00">
                        </div>
                        @error('price') <div class="text-danger extra-small mt-1" style="font-size: 0.8rem;">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold small text-uppercase">Gallery Images</label>
                        <div class="upload-grid" id="uploadContainer">
                            
                            {{-- Existing Morph Images --}}
                            @foreach($category->images as $image)
                                <div class="upload-box shadow-sm border-solid" style="border-style: solid; border-color: #ddd;">
                                    <img src="{{ asset('storage/' . $image->path) }}" class="preview-img">
                                    <button type="button" class="remove-img-btn shadow-sm" onclick="removeExistingImage(this, '{{ $image->id }}')">
                                        <i class="bi bi-trash3-fill" style="font-size: 12px;"></i>
                                    </button>
                                </div>
                            @endforeach

                            {{-- New Image Upload Trigger --}}
                            <div class="upload-box shadow-sm" onclick="this.querySelector('input').click()">
                                <i class="bi bi-plus-lg fs-3 text-muted"></i>
                                <input type="file" name="images[]" accept="image/*" onchange="handlePreview(this)">
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block">Click the (+) to add new images or the trash icon to remove existing ones.</small>
                        @error('images') <div class="text-danger extra-small mt-1" style="font-size: 0.8rem;">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="card-footer bg-white border-0 pb-4 px-4 mt-3 d-flex gap-2">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-light py-3 fw-bold" style="border-radius: 12px; flex: 1;">
                    CANCEL
                </a>
                <button class="btn btn-dark py-3 shadow-sm fw-bold" type="submit" style="border-radius: 12px; flex: 2;">
                    <i class="bi bi-check-circle me-2"></i> UPDATE CATEGORY
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .bg-light { background-color: #f8f9fa !important; }
    .form-control:focus, .form-select:focus { background-color: #f2f2f2 !important; box-shadow: none; border: 1px solid #ddd; }
    .btn-check:checked + .btn-outline-light { background-color: #212529 !important; color: #fff !important; font-weight: bold; }
    
    .upload-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 110px)); gap: 15px; }
    .upload-box { width: 110px; height: 110px; background: #f8f9fa; border-radius: 12px; display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative; overflow: hidden; border: 2px dashed #e0e0e0; transition: 0.2s; }
    .upload-box:hover { border-color: #bbb; background: #f1f1f1; }
    .upload-box input { display: none; }
    .upload-box img { width: 100%; height: 100%; object-fit: cover; position: absolute; top:0; left:0; }
    
    .remove-img-btn { position: absolute; top: 5px; right: 5px; background: white; border: none; border-radius: 50%; width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; color: #dc3545; z-index: 10; border: 1px solid #eee; }
</style>
@endsection

@push('scripts')
<script>
    /**
     * Handles the preview for NEWLY uploaded images
     */
    function handlePreview(input) {
        if (input.files && input.files[0]) {
            const box = input.closest('.upload-box');
            const reader = new FileReader();

            reader.onload = function(e) {
                // Prepare box for preview
                box.querySelector('.bi-plus-lg').style.display = 'none';

                // Create Image Element
                const img = document.createElement('img');
                img.src = e.target.result;
                box.appendChild(img);

                // Create Delete Button for new image
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'remove-img-btn shadow-sm';
                btn.innerHTML = '<i class="bi bi-x-lg" style="font-size: 12px;"></i>';
                btn.onclick = (event) => {
                    event.stopPropagation();
                    box.remove(); // Simply remove from DOM
                };
                box.appendChild(btn);

                // Add a new empty box trigger
                if (box === document.getElementById('uploadContainer').lastElementChild) {
                    addNewBox();
                }
            };
            reader.readAsDataURL(input.files[0]);
            box.onclick = null; 
        }
    }

    /**
     * Adds a new upload placeholder to the grid
     */
    function addNewBox() {
        const container = document.getElementById('uploadContainer');
        const div = document.createElement('div');
        div.className = 'upload-box shadow-sm';
        div.onclick = function() { this.querySelector('input').click(); };
        div.innerHTML = `
            <i class="bi bi-plus-lg fs-3 text-muted"></i>
            <input type="file" name="images[]" accept="image/*" onchange="handlePreview(this)">
        `;
        container.appendChild(div);
    }

    /**
     * Logic for deleting ALREADY EXISTING images (Morph IDs)
     */
    function removeExistingImage(btn, imageId) {
        const container = document.getElementById('deletedImagesContainer');
        const box = btn.closest('.upload-box');

        // Create hidden input for backend to know which ID to delete
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'deleteImages[]';
        input.value = imageId;
        container.appendChild(input);

        // Remove the box from UI
        box.remove();
    }
</script>
@endpush