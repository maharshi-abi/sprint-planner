@include('partials.sprint-goal-styles')
@if(!empty($goal) && trim(strip_tags($goal)) !== '')
<div class="sprint-goal-html {{ $class ?? '' }}">
    {!! $goal !!}
</div>
@elseif(!empty($emptyText))
<p class="text-slate-500 dark:text-slate-400 text-sm {{ $class ?? '' }}">{{ $emptyText }}</p>
@endif
