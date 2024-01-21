import preset from './vendor/filament/support/tailwind.config.preset'

export default {
    presets: [preset],
    mode: 'jit',
    darkMode: 'class',
    content: [
        './app/Filament/**/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './node_modules/flowbite/**/*.js',
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('flowbite/plugin')
    ],
}
