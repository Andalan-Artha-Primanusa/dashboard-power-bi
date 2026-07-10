@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-xl border-[#BD9B75] bg-white text-[#BD9B75] shadow-none placeholder-[#BD9B75]/70 focus:border-[#BD9B75] focus:ring-[#BD9B75]']) }}>
