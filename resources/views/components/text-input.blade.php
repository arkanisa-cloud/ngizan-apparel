@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border border-hairline bg-soft-cloud focus:border-ink focus:ring-0 rounded-full px-4 py-2.5 text-xs text-ink placeholder-stone transition w-full']) }}>
