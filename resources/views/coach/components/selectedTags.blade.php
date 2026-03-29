<div class="mb-3">
    <label for="tags" class="form-label">Tags</label>
    <select name="tags[]" id="tags" class="form-select select2" multiple>
        @foreach ($tags as $tag)
            <option value="{{ $tag->id }}"
                {{ in_array($tag->id, old('tags', $selectedTags ?? [])) ? 'selected' : '' }}>
                {{ $tag->name }}
            </option>
        @endforeach
    </select>
    <small class="form-text text-muted">Hold CTRL (Windows) or CMD (Mac) to select
        multiple tags.</small>
</div>
