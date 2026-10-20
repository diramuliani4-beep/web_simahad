@props(['size' => 48, 'class' => ''])
<svg viewBox="0 0 64 64" width="{{ $size }}" height="{{ $size }}" fill="none" xmlns="http://www.w3.org/2000/svg" class="{{ $class }}">
    <!-- Outer shield / dome silhouette in deep maroon -->
    <path d="M32 6 L52 19 L52 38 L43 46 L43 54 L32 58 L21 54 L21 46 L12 38 L12 19 Z" 
          stroke="#781023" stroke-width="3.5" stroke-linejoin="round" fill="none"/>
    
    <!-- Inner top roof peak in gold/yellow -->
    <path d="M32 12 L46 22 L41 26 L32 19 L23 26 L18 22 Z" 
          fill="#F5A623"/>

    <!-- Center Gold Star -->
    <path d="M32 24 L33.2 27.8 L37.2 27.8 L34 30.1 L35.2 33.9 L32 31.6 L28.8 33.9 L30 30.1 L26.8 27.8 L30.8 27.8 Z" 
          fill="#F5A623"/>

    <!-- Inner lower gate structure in maroon -->
    <path d="M25 36 H39 V49 L32 52 L25 49 Z" 
          stroke="#781023" stroke-width="2.5" fill="none" stroke-linejoin="round"/>
    
    <!-- Symmetrical arch accents -->
    <path d="M19 28 L23 31 V47 L19 44 Z" fill="#781023"/>
    <path d="M45 28 L41 31 V47 L45 44 Z" fill="#781023"/>
</svg>
