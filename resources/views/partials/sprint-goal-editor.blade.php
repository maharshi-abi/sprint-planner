@php
    $goalValue = old('goal', $goal ?? '');
@endphp
<div>
    <label class="block text-sm font-medium mb-1">Sprint Goal</label>
    <input type="hidden" name="goal" id="sprint-goal-input" value="{!! e($goalValue) !!}">
    <trix-editor input="sprint-goal-input" class="trix-content bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg min-h-[120px]"></trix-editor>
    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Use the toolbar for headings, lists, links, and formatting. Content is saved as HTML.</p>
</div>

@include('partials.sprint-goal-styles')
@once
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trix@2.1.13/dist/trix.css">
<style>trix-toolbar .trix-button-group--file-tools { display: none; }</style>
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/trix@2.1.13/dist/trix.umd.min.js"></script>
@endpush
@endonce
