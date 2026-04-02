@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-orange-200 focus:border-orange-400 focus:ring-orange-300 rounded-lg shadow-sm bg-orange-50/40']) }}>
